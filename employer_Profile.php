<?php
header('Content-Type: application/json; charset=UTF-8');
session_start();

// Database connection
include ('connection.php');

$response = ['status' => 'error', 'message' => 'Invalid request'];
$action = $_POST['action'] ?? '';

// Helper function to sanitize input
function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

switch ($action) {
    case 'get_profile':
        if (!isset($_SESSION['email']) || $_SESSION['role'] != 'employer') {
            $response['message'] = 'Not logged in as employer';
            break;
        }

        $employer_id = $_SESSION['email'];
        $stmt = $conn->prepare('SELECT * FROM employer WHERE email = ?');
        $stmt->bind_param('i', $employer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $employer = $result->fetch_assoc();

            // Build full URL for profile image if it exists
            if (!empty($employer['Image'])) {
                $employer['ImageUrl'] = 'data:image/jpeg;base64,' . base64_encode($employer['Image']);
            } else {
                $employer['ImageUrl'] = '';
            }

            $response = [
                'status' => 'success',
                'profile' => $employer
            ];
        } else {
            $response['message'] = 'Profile not found';
        }
        break;

    case 'update_profile':
        if (!isset($_SESSION['email']) || $_SESSION['role'] != 'employer') {
            $response['message'] = 'Not logged in as employer';
            break;
        }

        $employer_id = $_SESSION['email'];

        // Basic info
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $contact = sanitizeInput($_POST['contact'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');

        $stmt = $conn->prepare('UPDATE employer SET 
            Name = ?, Email = ?, Contact = ?, Address = ?, Description = ?
            WHERE email = ?');
        $stmt->bind_param('sssssi', $name, $email, $contact, $address, $description, $employer_id);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Profile updated successfully'];
        } else {
            $response['message'] = 'Failed to update profile: ' . $conn->error;
        }
        break;

    case 'upload_image':
        if (!isset($_SESSION['email']) || $_SESSION['role'] != 'employer') {
            $response['message'] = 'Not logged in as employer';
            break;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $employer_id = $_SESSION['email'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);

            $stmt = $conn->prepare('UPDATE employer SET Image = ? WHERE EmpID = ?');
            $stmt->bind_param('si', $image_data, $employer_id);

            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'message' => 'Company logo updated successfully',
                    'imageUrl' => 'data:image/jpeg;base64,' . base64_encode($image_data)
                ];
            } else {
                $response['message'] = 'Failed to update database: ' . $conn->error;
            }
        } else {
            $response['message'] = 'No file uploaded or upload error';
        }
        break;

    case 'change_password':
        if (!isset($_SESSION['email']) || $_SESSION['role'] != 'employer') {
            $response['message'] = 'Not logged in as employer';
            break;
        }

        $employer_id = $_SESSION['email'];
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Verify current password
        $stmt = $conn->prepare('SELECT Password FROM employer WHERE EmpID = ?');
        $stmt->bind_param('i', $employer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $employer = $result->fetch_assoc();

        if (!password_verify($current_password, $employer['Password'])) {
            $response['message'] = 'Current password is incorrect';
            break;
        }

        if ($new_password !== $confirm_password) {
            $response['message'] = 'New passwords do not match';
            break;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE employer SET Password = ? WHERE EmpID = ?');
        $stmt->bind_param('si', $hashed_password, $employer_id);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Password updated successfully'];
        } else {
            $response['message'] = 'Failed to update password: ' . $conn->error;
        }
        break;

    default:
        $response['message'] = 'Unknown action';
}

echo json_encode($response);
$conn->close();
?>

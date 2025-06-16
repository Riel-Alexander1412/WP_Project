<?php
header('Content-Type: application/json; charset=UTF-8');
session_start();

include ('connection.php');

$response = ['status' => 'error', 'message' => 'Invalid request'];
$action = $_POST['action'] ?? '';

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

switch ($action) {
    case 'get_profile':
        if (!isset($_SESSION['email']) || $_SESSION['role'] != 'employer') {
            $response['message'] = 'Not logged in as employer';
            break;
        }

        $employer_email = $_SESSION['email'];
        $stmt = $conn->prepare('SELECT * FROM employer WHERE email = ?');
        $stmt->bind_param('s', $employer_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $employer = $result->fetch_assoc();

            // Build image URL from datastore
            if (!empty($employer['Image'])) {
                $imagePath = 'datastore/' . $employer['Image'];
                if (file_exists($imagePath)) {
                    $employer['ImageUrl'] = $imagePath;
                } else {
                    $employer['ImageUrl'] = '';
                }
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

    case 'upload_image':
        if (!isset($_SESSION['email']) || $_SESSION['role'] != 'employer') {
            $response['message'] = 'Not logged in as employer';
            break;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $employer_email = $_SESSION['email'];
            $upload_dir = 'datastore/';

            // Create directory if needed
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Validate image type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            if (!in_array($file_type, $allowed_types)) {
                $response['message'] = 'Only JPG, PNG, and GIF files are allowed';
                break;
            }

            // Generate unique filename
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = 'employer_' . $employer_email . '_' . time() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;

            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                // Update database with filename (not full path)
                $stmt = $conn->prepare('UPDATE employer SET Image = ? WHERE email = ?');
                $stmt->bind_param('ss', $file_name, $employer_email);

                if ($stmt->execute()) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Company logo updated successfully',
                        'imageUrl' => $file_path
                    ];
                } else {
                    unlink($file_path); // Clean up if DB update fails
                    $response['message'] = 'Failed to update database: ' . $conn->error;
                }
            } else {
                $response['message'] = 'Failed to upload file';
            }
        } else {
            $response['message'] = 'No file uploaded or upload error';
        }
        break;

    // [Keep other cases (update_profile, change_password) unchanged]
    // ... rest of your existing code ...

    default:
        $response['message'] = 'Unknown action';
}

echo json_encode($response);
$conn->close();
?>

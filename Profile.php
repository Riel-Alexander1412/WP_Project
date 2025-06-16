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
        if (!isset($_SESSION['loggedin'])) {
            $response['message'] = 'Not logged in';
            break;
        }

        $user_id = $_SESSION['email'];
        $profile_id = $_POST['profile_id'] ?? $user_id;
        $is_own_profile = ($profile_id == $user_id);

        $stmt = $conn->prepare('SELECT * FROM user WHERE ID = ?');
        $stmt->bind_param('i', $profile_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $results = $result->fetch_all(MYSQLI_ASSOC);
            $profile = $results[0];

            // Build full URL for profile image if it exists
            if (!empty($profile['Image'])) {
                $profile['ImageUrl'] = $profile['Image'];
            } else {
                $profile['ImageUrl'] = '';
            }

            // Build full URL for resume if it exists
            if (!empty($profile['Resume'])) {
                $profile['ResumeUrl'] = $profile['Resume'];
            }

            // Remove sensitive data if viewing someone else's profile
            if (!$is_own_profile) {
                unset($profile['Password']);
                unset($profile['DoB']);
            }

            $response = [
                'status' => 'success',
                'profile' => $profile,
                'is_own_profile' => $is_own_profile
            ];
        } else {
            $response['message'] = 'Profile not found';
        }
        break;

    case 'update_profile':
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'Not logged in';
            break;
        }

        $user_id = $_SESSION['user_id'];

        // Basic info
        $name = sanitizeInput($_POST['name'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');
        $coo = sanitizeInput($_POST['coo'] ?? '');
        $gender = sanitizeInput($_POST['gender'] ?? '');
        $hiedu = sanitizeInput($_POST['hiedu'] ?? '');
        $unifeat = sanitizeInput($_POST['unifeat'] ?? '');

        $stmt = $conn->prepare('UPDATE user SET 
            Name = ?, PhoneNum = ?, Address = ?, COO = ?, 
            Gender = ?, HiEdu = ?, UniFeat = ? 
            WHERE ID = ?');
        $stmt->bind_param('sssssssi', $name, $phone, $address, $coo,
            $gender, $hiedu, $unifeat, $user_id);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Profile updated successfully'];
        } else {
            $response['message'] = 'Failed to update profile: ' . $conn->error;
        }
        break;

    case 'upload_resume':
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'Not logged in';
            break;
        }

        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $user_id = $_SESSION['user_id'];
            $upload_dir = 'uploads/resumes/';

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
            $file_name = 'resume_' . $user_id . '_' . time() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path)) {
                $stmt = $conn->prepare('UPDATE user SET Resume = ? WHERE ID = ?');
                $stmt->bind_param('si', $file_name, $user_id);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Resume uploaded successfully', 'file_name' => $file_name];
                } else {
                    unlink($file_path);
                    $response['message'] = 'Failed to update database: ' . $conn->error;
                }
            } else {
                $response['message'] = 'Failed to upload file';
            }
        } else {
            $response['message'] = 'No file uploaded or upload error';
        }
        break;

    case 'upload_image':
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'Not logged in';
            break;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $user_id = $_SESSION['user_id'];
            $upload_dir = 'Datastore/Images/';

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Validate image
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];

            if (!in_array($file_type, $allowed_types)) {
                $response['message'] = 'Only JPG, PNG, and GIF files are allowed';
                break;
            }

            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_name = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $stmt = $conn->prepare('UPDATE user SET Image = ? WHERE ID = ?');
                $stmt->bind_param('si', $file_name, $user_id);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Profile image uploaded successfully', 'file_name' => $file_name];
                } else {
                    unlink($file_path);
                    $response['message'] = 'Failed to update database: ' . $conn->error;
                }
            } else {
                $response['message'] = 'Failed to upload file';
            }
        } else {
            $response['message'] = 'No file uploaded or upload error';
        }
        break;

    default:
        $response['message'] = 'Unknown action';
}

echo json_encode($response);
$conn->close();
?>

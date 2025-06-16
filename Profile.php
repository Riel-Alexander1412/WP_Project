<?php
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
    // In the 'get_profile' case of your switch statement:
    case 'get_profile':
        if (!isset($_SESSION['loggedin'])) {
            $response['message'] = 'Not logged in';
            break;
        }

        $user_email = $_SESSION['email'];
        $profile_email = $_POST['profile_email'] ?? $user_email;
        $is_own_profile = ($profile_email == $user_email);

        $stmt = $conn->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->bind_param('s', $profile_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $results = $result->fetch_all(MYSQLI_ASSOC);
            $profile = $results[0];

            if (!empty($profile['Image'])) {
                if (strpos($profile['Image'], 'http') === 0) {
                    $profile['ImageUrl'] = $profile['Image'];
                } else {
                    $base_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
                    $profile['ImageUrl'] = $base_url . '/' . ltrim($profile['Image'], '/');
                }

                if (!file_exists($profile['Image'])) {
                    $profile['ImageUrl'] = '';  // Fallback to no image
                }
            } else {
                $profile['ImageUrl'] = '';
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
        if (!isset($_SESSION['email'])) {
            $response['message'] = 'Not logged in';
            break;
        }

        $user_email = $_SESSION['email'];

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
            WHERE email = ?');
        $stmt->bind_param('ssssssss', $name, $phone, $address, $coo,
            $gender, $hiedu, $unifeat, $user_email);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Profile updated successfully'];
        } else {
            $response['message'] = 'Failed to update profile: ' . $conn->error;
        }
        break;

    case 'upload_resume':
        if (!isset($_SESSION['email'])) {
            $response['message'] = 'Not logged in';
            break;
        }

        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $user_email = $_SESSION['email'];
            $upload_dir = 'uploads/resumes/';

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
            $file_name = 'resume_' . $user_email . '_' . time() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path)) {
                $stmt = $conn->prepare('UPDATE user SET Resume = ? WHERE email = ?');
                $stmt->bind_param('ss', $file_name, $user_email);

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
        if (!isset($_SESSION['email'])) {
            $response['message'] = 'Not logged in';
            break;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $user_email = $_SESSION['email'];
            $upload_dir = 'Datastore/';

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
            $file_name = 'profile_' . $user_email . '_' . time() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $stmt = $conn->prepare('UPDATE user SET Image = ? WHERE email = ?');
                $stmt->bind_param('ss', $file_name, $user_email);

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

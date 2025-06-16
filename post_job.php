<?php
session_start();
include ('connection.php');

// Check if user is logged in as employer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employer') {
    header('Location: Login.php');
    exit();
}

$employer_id = $_SESSION['email'];
$error = '';
$success = '';

// Form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $job_level = mysqli_real_escape_string($conn, $_POST['job_level']);
    $min_education = mysqli_real_escape_string($conn, $_POST['min_education']);
    $course_type = mysqli_real_escape_string($conn, $_POST['course_type']);
    $contract_type = mysqli_real_escape_string($conn, $_POST['contract_type']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $tags = mysqli_real_escape_string($conn, $_POST['tags']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Basic validation
    if (empty($position) || empty($location) || empty($salary)) {
        $error = 'Position, Location, and Salary are required fields.';
    } else {
        // Insert job into database
        $sql = "INSERT INTO job_listing (
            Position, 
            EmployerID, 
            JbLV, 
            MinLV, 
            CourseType, 
            CType, 
            Salary, 
            Tags, 
            PostDate, 
            Location, 
            Status,
            Description
        ) VALUES (
            '$position',
            $employer_id,
            '$job_level',
            '$min_education',
            '$course_type',
            '$contract_type',
            '$salary',
            '$tags',
            CURDATE(),
            '$location',
            'New',
            '$description'
        )";

        if (mysqli_query($conn, $sql)) {
            $success = 'Job posted successfully!';
            // Clear form if needed
            $_POST = array();
        } else {
            $error = 'Error posting job: ' . mysqli_error($conn);
        }
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job</title>
    <link rel="stylesheet" href="CSS/post_job.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">Job Finder System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="employer_Profile.html">Manage Profile</a>
                <a class="nav-link" href="ManageListings.php">Manage Jobs</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Post New Job Listing</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" class="job-form">
            <div class="form-group">
                <label for="position">Job Position*</label>
                <input type="text" id="position" name="position" required 
                       value="<?php echo isset($_POST['position']) ? htmlspecialchars($_POST['position']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="description">Job Description</label>
                <textarea id="description" name="description" rows="5"><?php
echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
?></textarea>
            </div>

            <div class="form-group">
                <label for="job_level">Job Level*</label>
                <select id="job_level" name="job_level" required>
                    <option value="Entry" <?php echo (isset($_POST['job_level']) && $_POST['job_level'] == 'Entry' ? 'selected' : ''); ?>>Entry Level</option>
                    <option value="Intermediate" <?php echo (isset($_POST['job_level']) && $_POST['job_level'] == 'Intermediate' ? 'selected' : ''); ?>>Intermediate</option>
                    <option value="Senior" <?php echo (isset($_POST['job_level']) && $_POST['job_level'] == 'Senior' ? 'selected' : ''); ?>>Senior</option>
                    <option value="Executive" <?php echo (isset($_POST['job_level']) && $_POST['job_level'] == 'Executive' ? 'selected' : ''); ?>>Executive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="min_education">Minimum Education*</label>
                <select id="min_education" name="min_education" required>
                    <option value="SPM/GCSE" <?php echo (isset($_POST['min_education']) && $_POST['min_education'] == 'SPM/GCSE' ? 'selected' : ''); ?>>SPM/GCSE</option>
                    <option value="Diploma" <?php echo (isset($_POST['min_education']) && $_POST['min_education'] == 'Diploma' ? 'selected' : ''); ?>>Diploma</option>
                    <option value="Degree" <?php echo (isset($_POST['min_education']) && $_POST['min_education'] == 'Degree' ? 'selected' : ''); ?>>Degree</option>
                    <option value="Master" <?php echo (isset($_POST['min_education']) && $_POST['min_education'] == 'Master' ? 'selected' : ''); ?>>Master</option>
                    <option value="PhD" <?php echo (isset($_POST['min_education']) && $_POST['min_education'] == 'PhD' ? 'selected' : ''); ?>>PhD</option>
                </select>
            </div>

            <div class="form-group">
                <label for="course_type">Field of Study</label>
                <input type="text" id="course_type" name="course_type" 
                       value="<?php echo isset($_POST['course_type']) ? htmlspecialchars($_POST['course_type']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="contract_type">Contract Type*</label>
                <select id="contract_type" name="contract_type" required>
                    <option value="Full Time" <?php echo (isset($_POST['contract_type']) && $_POST['contract_type'] == 'Full Time' ? 'selected' : ''); ?>>Full Time</option>
                    <option value="Part Time" <?php echo (isset($_POST['contract_type']) && $_POST['contract_type'] == 'Part Time' ? 'selected' : ''); ?>>Part Time</option>
                    <option value="Contract" <?php echo (isset($_POST['contract_type']) && $_POST['contract_type'] == 'Contract' ? 'selected' : ''); ?>>Contract</option>
                    <option value="Internship" <?php echo (isset($_POST['contract_type']) && $_POST['contract_type'] == 'Internship' ? 'selected' : ''); ?>>Internship</option>
                </select>
            </div>

            <div class="form-group">
                <label for="salary">Salary*</label>
                <input type="text" id="salary" name="salary" required 
                       value="<?php echo isset($_POST['salary']) ? htmlspecialchars($_POST['salary']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="location">Location*</label>
                <input type="text" id="location" name="location" required 
                       value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="tags">Tags (comma separated)</label>
                <input type="text" id="tags" name="tags" 
                       value="<?php echo isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : ''; ?>">
            </div>

            <button type="submit" class="btn btn-primary">Post Job</button>
            <a href="ManageListings.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>

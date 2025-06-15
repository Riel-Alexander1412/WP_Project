<?php
session_start();
include ('connection.php');

// Check if user is logged in as employer
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'employer') {
    header('Location: Login.php');
    exit();
}

$employer_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Check if job_id is provided
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header('Location: ManageListings.php');
    exit();
}

$job_id = intval($_GET['job_id']);

// Fetch job details
$sql = "SELECT * FROM job_listing WHERE ListingID = $job_id AND EmployerID = $employer_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header('Location: ManageListings.php');
    exit();
}

$job = mysqli_fetch_assoc($result);

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
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Basic validation
    if (empty($position) || empty($location) || empty($salary)) {
        $error = 'Position, Location, and Salary are required fields.';
    } else {
        // Update job in database
        $sql = "UPDATE job_listing SET
                Position = '$position',
                JbLV = '$job_level',
                MinLV = '$min_education',
                CourseType = '$course_type',
                CType = '$contract_type',
                Salary = '$salary',
                Tags = '$tags',
                Location = '$location',
                Description = '$description',
                Status = '$status'
                WHERE ListingID = $job_id AND EmployerID = $employer_id";

        if (mysqli_query($conn, $sql)) {
            $success = 'Job updated successfully!';
            // Refresh job data
            $sql = "SELECT * FROM job_listing WHERE ListingID = $job_id";
            $result = mysqli_query($conn, $sql);
            $job = mysqli_fetch_assoc($result);
        } else {
            $error = 'Error updating job: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Listing</title>
    <link rel="stylesheet" href="css/edit_job.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">Job Finder System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="employer_Profile.html">Dashboard</a>
                <a class="nav-link" href="ManageListings.php">Manage Jobs</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Edit Job Listing: <?php echo htmlspecialchars($job['Position']); ?></h2>
        
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
                       value="<?php echo htmlspecialchars($job['Position']); ?>">
            </div>

            <div class="form-group">
                <label for="description">Job Description</label>
                <textarea id="description" name="description" rows="5"><?php
echo htmlspecialchars($job['Description']);
?></textarea>
            </div>

            <div class="form-group">
                <label for="job_level">Job Level*</label>
                <select id="job_level" name="job_level" required>
                    <option value="Entry" <?php echo $job['JbLV'] == 'Entry' ? 'selected' : ''; ?>>Entry Level</option>
                    <option value="Intermediate" <?php echo $job['JbLV'] == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                    <option value="Senior" <?php echo $job['JbLV'] == 'Senior' ? 'selected' : ''; ?>>Senior</option>
                    <option value="Executive" <?php echo $job['JbLV'] == 'Executive' ? 'selected' : ''; ?>>Executive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="min_education">Minimum Education*</label>
                <select id="min_education" name="min_education" required>
                    <option value="SPM/GCSE" <?php echo $job['MinLV'] == 'SPM/GCSE' ? 'selected' : ''; ?>>SPM/GCSE</option>
                    <option value="Diploma" <?php echo $job['MinLV'] == 'Diploma' ? 'selected' : ''; ?>>Diploma</option>
                    <option value="Degree" <?php echo $job['MinLV'] == 'Degree' ? 'selected' : ''; ?>>Degree</option>
                    <option value="Master" <?php echo $job['MinLV'] == 'Master' ? 'selected' : ''; ?>>Master</option>
                    <option value="PhD" <?php echo $job['MinLV'] == 'PhD' ? 'selected' : ''; ?>>PhD</option>
                </select>
            </div>

            <div class="form-group">
                <label for="course_type">Field of Study</label>
                <input type="text" id="course_type" name="course_type" 
                       value="<?php echo htmlspecialchars($job['CourseType']); ?>">
            </div>

            <div class="form-group">
                <label for="contract_type">Contract Type*</label>
                <select id="contract_type" name="contract_type" required>
                    <option value="Full Time" <?php echo $job['CType'] == 'Full Time' ? 'selected' : ''; ?>>Full Time</option>
                    <option value="Part Time" <?php echo $job['CType'] == 'Part Time' ? 'selected' : ''; ?>>Part Time</option>
                    <option value="Contract" <?php echo $job['CType'] == 'Contract' ? 'selected' : ''; ?>>Contract</option>
                    <option value="Internship" <?php echo $job['CType'] == 'Internship' ? 'selected' : ''; ?>>Internship</option>
                </select>
            </div>

            <div class="form-group">
                <label for="salary">Salary*</label>
                <input type="text" id="salary" name="salary" required 
                       value="<?php echo htmlspecialchars($job['Salary']); ?>">
            </div>

            <div class="form-group">
                <label for="location">Location*</label>
                <input type="text" id="location" name="location" required 
                       value="<?php echo htmlspecialchars($job['Location']); ?>">
            </div>

            <div class="form-group">
                <label for="tags">Tags (comma separated)</label>
                <input type="text" id="tags" name="tags" 
                       value="<?php echo htmlspecialchars($job['Tags']); ?>">
            </div>

            <div class="form-group">
                <label for="status">Job Status*</label>
                <select id="status" name="status" required>
                    <option value="New" <?php echo $job['Status'] == 'New' ? 'selected' : ''; ?>>New</option>
                    <option value="Filled" <?php echo $job['Status'] == 'Filled' ? 'selected' : ''; ?>>Filled</option>
                    <option value="Closed" <?php echo $job['Status'] == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Job</button>
            <a href="ManageListings.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>

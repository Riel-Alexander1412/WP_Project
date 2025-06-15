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

// Handle job deletion
if (isset($_GET['delete'])) {
    $job_id = intval($_GET['delete']);

    // Verify the job belongs to this employer
    $check_sql = "SELECT EmployerID FROM job_listing WHERE ListingID = $job_id";
    $check_result = mysqli_query($conn, $check_sql);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $job = mysqli_fetch_assoc($check_result);
        if ($job['EmployerID'] == $employer_id) {
            // Delete the job
            $delete_sql = "DELETE FROM job_listing WHERE ListingID = $job_id";

            if (mysqli_query($conn, $delete_sql)) {
                $success = 'Job listing deleted successfully!';
            } else {
                $error = 'Error deleting job listing: ' . mysqli_error($conn);
            }
        } else {
            $error = "You don't have permission to delete this job.";
        }
    } else {
        $error = 'Job not found.';
    }
}

// Fetch all jobs posted by this employer
$jobs = array();
$sql = "SELECT * FROM job_listing WHERE EmployerID = $employer_id ORDER BY PostDate DESC";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $jobs[] = $row;
    }
}

// Get employer info for the header
$employer_sql = "SELECT Name FROM employer WHERE EmpID = $employer_id";
$employer_result = mysqli_query($conn, $employer_sql);
$employer = mysqli_fetch_assoc($employer_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Listings</title>
    <link rel="stylesheet" href="css/ManageListings.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">Job Finder System</a>
            <div class="navbar-nav">
                <span class="navbar-text">Welcome, <?php echo htmlspecialchars($employer['Name']); ?></span>
                <a class="nav-link" href="employer_Profile.html">Dashboard</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Manage Your Job Listings</h2>
            <a href="post_job.php" class="btn btn-success">Post New Job</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (empty($jobs)): ?>
            <div class="alert alert-info">You haven't posted any jobs yet. <a href="post_job.php">Post your first job now</a>.</div>
        <?php else: ?>
            <div class="job-grid">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-card">
                        <div class="job-header">
                            <div>
                                <h3 class="job-title"><?php echo htmlspecialchars($job['Position']); ?></h3>
                                <p class="job-date">Posted on: <?php echo date('M d, Y', strtotime($job['PostDate'])); ?></p>
                            </div>
                            <span class="badge <?php echo $job['Status'] == 'New' ? 'status-new' : 'status-filled'; ?>">
                                <?php echo htmlspecialchars($job['Status']); ?>
                            </span>
                        </div>
                        
                        <div class="job-detail">
                            <strong>Location:</strong> <?php echo htmlspecialchars($job['Location']); ?>
                        </div>
                        <div class="job-detail">
                            <strong>Salary:</strong> <?php echo htmlspecialchars($job['Salary']); ?>
                        </div>
                        <div class="job-detail">
                            <strong>Contract Type:</strong> <?php echo htmlspecialchars($job['CType']); ?>
                        </div>
                        
                        <div class="job-actions">
                            <a href="view_applications.php?job_id=<?php echo $job['ListingID']; ?>" class="btn btn-primary">
                                View Applications
                            </a>
                            <div class="btn-group">
                                <a href="edit_job.php?job_id=<?php echo $job['ListingID']; ?>" class="btn btn-warning">
                                    Edit
                                </a>
                                <a href="ManageListings.php?delete=<?php echo $job['ListingID']; ?>" class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this job listing?');">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

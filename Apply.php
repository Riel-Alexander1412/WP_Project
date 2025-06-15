<?php
    include("connection.php");
    include("control.php");
    session_start();


    $user_id = $_SESSION['email'];
    $job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

    if ($job_id <= 0) {
        echo "<script>alert('Invalid job selection.'); window.location.href='Listing.php';</script>";
        exit;
    }

    $job = null;
    $query = "SELECT * FROM job_listing WHERE ListingID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $job_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $job = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Job not found.'); window.location.href='Listing.php';</script>";
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $notes = trim($_POST['notes']);

        if (empty($notes)) {
            echo "<script>alert('Notes cannot be empty.'); window.history.back();</script>";
            exit;
        }

        $check = mysqli_prepare($conn, "SELECT * FROM applied_jobs WHERE UserEmail=? AND JobID=?");
        mysqli_stmt_bind_param($check, "ii", $user_id, $job_id);
        mysqli_stmt_execute($check);
        $checkResult = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($checkResult) > 0) {
            echo "<script>
                alert('You have already applied for this job.');
                window.location.href = 'Listing.php';
            </script>";
        } else {
            $sql = "INSERT INTO applied_jobs(`UserEmail`, `JobID`, `Notes`) VALUES (?, ?, ?)";
            $insert = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($insert, "sis", $user_id, $job_id, $notes);
            if (mysqli_stmt_execute($insert)) {
                echo "<script>
                    alert('Application submitted successfully!');
                    window.location.href = 'Applied.php';
                </script>";
            } else {
                echo "<script>alert('Error submitting application.');</script>";
            }
        }
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for <?php echo htmlspecialchars($job['Position']); ?> | JobFinder</title>
    <link rel="stylesheet" href="css/Apply.css">
    <script>
        function validateForm() {
            const notes = document.getElementById("notes").value.trim();
            const errorDiv = document.getElementById("note-error");
            if (notes === "") {
                errorDiv.textContent = "Please enter your notes before applying.";
                return false;
            }
            errorDiv.textContent = "";
            return true;
        }
    </script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>JobFinder</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="Listing.php">Browse Jobs</a></li>
                    <li><a href="Applied.php">Applied Jobs</a></li>
                    <li><a href="Profile.php">Profile</a></li>
                    <li><a href="Login.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <?php if ($job): ?>
        <div class="apply-container">
            <div class="job-header">
                <h2>Apply for Position</h2>
                <h1><?php echo htmlspecialchars($job['Position']); ?></h1>
            </div>
            <div class="job-details">
                <div class="detail-card">
                    <h3><img src="Assets/Image/info.png" alt="Info Icon" height="30px" width="30px">Job Details</h3>
                    <ul>
                        <li><strong>Company Type:</strong> <?php echo htmlspecialchars($job['CType']); ?></li>
                        <li><strong>Location:</strong> <?php echo htmlspecialchars($job['Location']); ?></li>
                        <li><strong>Salary:</strong> RM<?php echo htmlspecialchars($job['Salary']); ?></li>
                        <li><strong>Posted Date:</strong> <?php echo htmlspecialchars($job['PostDate']); ?></li>
                    </ul>
                </div>
                <div class="detail-card">
                    <h3><img src="Assets/Image/hat.png" alt="Graduate Hat Icon" height="30px" width="30px">Requirements</h3>
                    <ul>
                        <li><strong>Level:</strong> <?php echo htmlspecialchars($job['JbLV']); ?></li>
                        <li><strong>Minimum Level:</strong> <?php echo htmlspecialchars($job['MinLV']); ?></li>
                        <li><strong>Course Type:</strong> <?php echo htmlspecialchars($job['CourseType']); ?></li>
                    </ul>
                </div>
            </div>
            <form class="application-form" name="applyForm" onsubmit="return validateForm();" method="post">
                <h3>Application Details</h3>
                <div class="form-group">
                    <label for="notes">Cover Letter/Notes <span class="required">*</span></label>
                    <textarea id="notes" name="notes" placeholder="Explain why you're a good fit for this position..." required></textarea>
                    <div id="note-error" class="error-message"></div>
                </div>
                <button type="submit" class="submit-btn">Submit Application</button>
            </form>
        </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-search-location"></i>
                    <span>JobFinder</span>
                </div>
                <div class="footer-links">
                    <a href="#">About Us</a>
                    <a href="#">Contact</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 JobFinder. All rights reserved. (Luqman Hakimi)</p>
            </div>
        </div>
    </footer>
</body>
</html>
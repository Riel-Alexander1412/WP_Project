<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply Job</title>
    <link rel="stylesheet" href="Apply.css">
    <script>
    function validateForm() {
        const notes = document.getElementById("notes").value.trim();
        const errorDiv = document.getElementById("note-error");
        if (notes === "") {
            errorDiv.textContent = "Please enter your notes before applying.";
            return false;
        } else {
            errorDiv.textContent = "";
            return true;
        }
    }
    </script>
</head>
<body>
    <header>
            <div>
                <h1>JobFinder</h1>
            </div>
            <div>
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
    <?php
    session_start();
    include("connection.php");

    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please login to apply for jobs.'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
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

// Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $notes = trim($_POST['notes']);

        if (empty($notes)) {
            echo "<script>alert('Notes cannot be empty.'); window.history.back();</script>";
            exit;
        }

        $check = mysqli_prepare($conn, "SELECT * FROM applied WHERE ID=? AND ListingID=?");
        mysqli_stmt_bind_param($check, "ii", $user_id, $job_id);
        mysqli_stmt_execute($check);
        $checkResult = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($checkResult) > 0) {
            echo "<script>
                alert('You have already applied for this job.');
                window.location.href = 'Listing.php';
            </script>";
        } else {
            $insert = mysqli_prepare($conn, "INSERT INTO applied (ID, ListingID, Notes) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert, "iis", $user_id, $job_id, $notes);
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

    <?php if ($job): ?>
        <div class="apply-container">
            <h2>Apply for: <?php echo htmlspecialchars($job['Position']); ?></h2>
            <div>
                <p><strong>Level:</strong> <?php echo htmlspecialchars($job['JbLV']); ?></p>
                <p><strong>Minimum Level:</strong> <?php echo htmlspecialchars($job['MinLV']); ?></p>
                <p><strong>Course Type:</strong> <?php echo htmlspecialchars($job['CourseType']); ?></p>
                <p><strong>Company Type:</strong> <?php echo htmlspecialchars($job['CType']); ?></p>
                <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['Salary']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['Location']); ?></p>
                <p><strong>Posted Date:</strong> <?php echo htmlspecialchars($job['PostDate']); ?></p>
            </div>
            <div>
                <form name="applyForm" onsubmit="return validateForm();" method="post">
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes"></textarea>
                    <div id="note-error" class="error-message"></div>
                    <input type="submit" value="Apply">
                </form>
            </div>
        </div>
    <?php endif; ?>
</body>
    <footer>
        <div>
            <p>&copy; 2025 JobFinder. All rights reserved. (Luqman Hakimi)</p>
        </div>
    </footer>
</html>

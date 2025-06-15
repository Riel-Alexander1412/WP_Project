<?php
session_start();
include("connection.php");

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unapply_id'])) {
    $listing_id = intval($_POST['unapply_id']);
    $delete = mysqli_prepare($conn, "DELETE FROM applied_jobs WHERE UserID = ? AND JobID = ?");
    mysqli_stmt_bind_param($delete, "ii", $user_id, $listing_id);
    mysqli_stmt_execute($delete);
    echo "<script>alert('You have unapplied from the job.'); window.location.href='Applied.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_notes_id'])) {
    $listing_id = intval($_POST['edit_notes_id']);
    $new_notes = trim($_POST['new_notes']);
    $update = mysqli_prepare($conn, "UPDATE applied_jobs SET Notes = ? WHERE UserID = ? AND JobID = ?");
    mysqli_stmt_bind_param($update, "sii", $new_notes, $user_id, $listing_id);
    mysqli_stmt_execute($update);
    echo "<script>alert('Notes updated.'); window.location.href='Applied.php';</script>";
    exit;
}

$query = "
    SELECT jl.*, a.Notes 
    FROM applied_jobs a
    JOIN job_listing jl ON a.JobID = jl.ListingID
    WHERE a.UserID = ?
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Applications | JobFinder</title>
    <link rel="stylesheet" href="css/Applied.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <i class="fas fa-search-location"></i>
                <h1>JobFinder</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="Listing.php"><i class="fas fa-briefcase"></i> Browse Jobs</a></li>
                    <li><a href="Applied.php" class="active"><i class="fas fa-check-circle"></i> Applied Jobs</a></li>
                    <li><a href="Profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="Login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="section-header">
            <h2><img src="image/clipboard.png" alt="Clipboard Icon" height="50px" width="50px"> Your Applications</h2>
            <p>View and manage your job applications</p>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="applications-container">
                <?php while ($job = mysqli_fetch_assoc($result)): ?>
                    <div class="application-card">
                        <div class="application-header">
                            <h3><?php echo htmlspecialchars($job['Position']); ?></h3>
                            <div class="job-meta">
                                <span><img src="image/building.png" alt="Building Icon" height="30px" width="30px"> <?php echo htmlspecialchars($job['CType']); ?></span><br>
                                <span><img src="image/location.png" alt="Location Icon" height="30px" width="30px"> <?php echo htmlspecialchars($job['Location']); ?></span><br>
                                <span><img src="image/money.png" alt="Salary Icon" height="30px" width="30px"> RM<?php echo htmlspecialchars($job['Salary']); ?></span>
                            </div>
                        </div>

                        <div class="application-body">
                            <?php if ($job['Status'] === 'Suspended'): ?>
                                <div class="cancelled-msg">

                                    <strong>Cancelled due to suspended</strong>
                                </div>
                                <div class="application-notes">
                                    <h4><img src="image/notes.png" alt="Sticky Note Icon" height="30px" width="30px"> Your Notes</h4>
                                    <p><?php echo nl2br(htmlspecialchars($job['Notes'] ?: 'No notes added')); ?></p>
                                </div>
                            <?php elseif (isset($_GET['edit']) && $_GET['edit'] == $job['ListingID']): ?>
                                <form method="post" class="notes-form">
                                    <div class="form-group">
                                        <label for="notes-<?php echo $job['ListingID']; ?>">Application Notes</label>
                                        <textarea id="notes-<?php echo $job['ListingID']; ?>" name="new_notes" placeholder="Add notes about your application..."><?php echo htmlspecialchars($job['Notes']); ?></textarea>
                                        <input type="hidden" name="edit_notes_id" value="<?php echo $job['ListingID']; ?>">
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <img src="image/save.png" alt="Save Icon" height="30px" width="30px"> Save
                                            </button>
                                            <a href="Applied.php" class="btn btn-secondary">
                                                <img src="image/cancel.png" alt="Cancel Icon" height="30px" width="30px"> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="application-notes">
                                    <h4><img src="image/notes.png" alt="Sticky Note Icon" height="30px" width="30px"> Your Notes</h4>
                                    <p><?php echo nl2br(htmlspecialchars($job['Notes'] ?: 'No notes added')); ?></p>
                                    <a href="Applied.php?edit=<?php echo $job['ListingID']; ?>" class="btn btn-edit">
                                        <img src="image/edit.png" alt="Edit Icon" height="30px" width="30px"> Edit Notes
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="application-footer">
                            <form method="POST" class="unapply-form" onsubmit="return confirm('Are you sure you want to withdraw this application?');">
                                <input type="hidden" name="unapply_id" value="<?php echo $job['ListingID']; ?>">
                                <button type="submit" class="btn btn-danger">
                                    <img src="image/trashw.png" alt="Trash Icon" height="30px" width="30px"> Withdraw Application
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <img src="image/clipboard.png" alt="Clipboard List Icon" height="30px" width="30px">
                <h3>No Applications Yet</h3>
                <p>You haven't applied for any jobs yet. Browse jobs to get started!</p>
                <a href="Listing.php" class="btn btn-primary">
                    <img src="image/briefcasew.png" alt="Briefcase Icon" height="30px" width="30px"> Browse Jobs
                </a>
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
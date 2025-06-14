<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    echo "Please <a href='login.php'>login</a> to view your applied jobs.";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unapply_id'])) {
    $listing_id = intval($_POST['unapply_id']);
    $delete = mysqli_prepare($conn, "DELETE FROM applied WHERE ID = ? AND ListingID = ?");
    mysqli_stmt_bind_param($delete, "ii", $user_id, $listing_id);
    mysqli_stmt_execute($delete);
    echo "<script>alert('You have unapplied from the job.'); window.location.href='Applied.php';</script>";
    exit;
}

// Handle notes update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_notes_id'])) {
    $listing_id = intval($_POST['edit_notes_id']);
    $new_notes = trim($_POST['new_notes']);
    $update = mysqli_prepare($conn, "UPDATE applied SET Notes = ? WHERE ID = ? AND ListingID = ?");
    mysqli_stmt_bind_param($update, "sii", $new_notes, $user_id, $listing_id);
    mysqli_stmt_execute($update);
    echo "<script>alert('Notes updated.'); window.location.href='Applied.php';</script>";
    exit;
}

$query = "
    SELECT jl.*, a.Notes 
    FROM applied a
    JOIN job_listing jl ON a.ListingID = jl.ListingID
    WHERE a.ID = ?
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
    <title>Applied Jobs</title>
    <link rel="stylesheet" href="Applied.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>JobFinder</h1>
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
    <h2>Your Applied Jobs</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($job = mysqli_fetch_assoc($result)): ?>
            <div class="job-card">
                <strong><?php echo htmlspecialchars($job['Position']); ?></strong><br>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['Location']); ?></p>
                <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['Salary']); ?></p>

                <!-- Show notes and edit form -->
                <?php if (isset($_GET['edit']) && $_GET['edit'] == $job['ListingID']): ?>
                    <!-- Show edit form -->
                    <form method="post" style="margin-top:10px;">
                        <textarea name="new_notes" rows="3" style="width:100%;"><?php echo htmlspecialchars($job['Notes']); ?></textarea>
                        <input type="hidden" name="edit_notes_id" value="<?php echo $job['ListingID']; ?>">
                        <input type="submit" value="Save" style="margin-top:6px;">
                        <a href="Applied.php" class="cancel-edit-link">Cancel</a>
                    </form>
                <?php else: ?>
                    <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($job['Notes'])); ?></p>
                    <!-- Edit Notes link -->
                    <a href="Applied.php?edit=<?php echo $job['ListingID']; ?>" class="edit-notes-link">Edit Notes</a>
                <?php endif; ?>

                <form method="POST" onsubmit="return confirm('Are you sure you want to unapply from this job?');">
                    <input type="hidden" name="unapply_id" value="<?php echo $job['ListingID']; ?>">
                    <input type="submit" value="Unapply">
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-applied-msg">You haven't applied for any jobs yet.</p>
    <?php endif; ?>
</body>
    <footer>
        <div>
            <p>&copy; 2025 JobFinder. All rights reserved. (Luqman Hakimi)</p>
        </div>
    </footer>
</html>

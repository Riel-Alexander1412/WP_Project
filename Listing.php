<?php
    include "connection.php";
    include "control.php";
    session_start();
?>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listing | JobFinder</title>
    <link rel="stylesheet" href="css/viewApplicants.css">
    <link rel="stylesheet" href="css/Listing.css">
</head>
    
<body>
    <header>
        <div class="header-container" style="display: flex; justify-content: space-between; width: 100%; padding: 0 20px; align-items:center;">
            <div class="logo">
                <h1>JobFinder</h1>
            </div>
            <nav style="display: flex; justify-content: space-between;">
                <ul style="display: flex; justify-content: space-between; gap:20px;">
                    <li><a href="Listing.php" class="active">Browse Jobs</a></li>
                    <li><a href="Applied.php">Applied Jobs</a></li>
                    <li><a href="Profile.html">Profile</a></li>
                    <li><a href="Login.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="main-content">
        <section class="browse-section">
            <div class="section-header">
                <h2>Find Your Dream Job</h2>
                <p>Browse through our latest job opportunities</p>
            </div>
            
            <div class="filter-section">
                <div class="tags">
                    <?php
                    include("connection.php");

                    $tagQuery = "SELECT DISTINCT tags FROM job_listing";
                    $tagResult = mysqli_query($conn, $tagQuery);

                    $selectedTag = isset($_GET['tags']) ? $_GET['tags'] : '';

                    if ($tagResult && mysqli_num_rows($tagResult) > 0) {
                        echo '<a href="Listing.php" class="tag-btn '.($selectedTag === '' ? 'active' : '').'">All Jobs</a>';
                        while ($row = mysqli_fetch_assoc($tagResult)) {
                            $tag = htmlspecialchars($row['tags']);
                            $active = ($selectedTag === $tag) ? 'active' : '';
                            echo "<a href='Listing.php?tags=$tag' class='tag-btn $active'>$tag</a>";
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
        
        <section class="job-listings">
            <?php
            if ($selectedTag) {
                $jobQuery = "SELECT * FROM job_listing WHERE tags = ? AND Status = 'Active'";
                $stmt = mysqli_prepare($conn, $jobQuery);
                mysqli_stmt_bind_param($stmt, "s", $selectedTag);
                mysqli_stmt_execute($stmt);
                $jobResult = mysqli_stmt_get_result($stmt);
            } else {
                $jobQuery = "SELECT * FROM job_listing WHERE Status = 'Active'";
                $jobResult = mysqli_query($conn, $jobQuery);
            }

            if ($jobResult && mysqli_num_rows($jobResult) > 0) {
                echo '<div class="job-grid">';
                while ($job = mysqli_fetch_assoc($jobResult)) {
                    $postDate = new DateTime($job['PostDate']);
                    $now = new DateTime();
                    $interval = $now->diff($postDate);
                    $daysAgo = $interval->days;
                    
                    echo "<a href='Apply.php?job_id=" . htmlspecialchars($job['ListingID']) . "' class='job-card'>";
                    echo "<div class='job-header'>";
                    echo "<h3>" . htmlspecialchars($job['Position']) . "</h3>";
                    echo "<span class='job-type'>" . htmlspecialchars($job['CType']) . "</span>";
                    echo "</div>";
                    
                    echo "<div class='job-details'>";
                    echo "<div class='detail-item'><img src='Assets/Image/money.png' alt='Salary Icon' class='icon'> RM " . htmlspecialchars($job['Salary']) . "</div>";
                    echo "<div class='detail-item'><img src='Assets/Image/location.png' alt='Location Icon' class='icon'> " . htmlspecialchars($job['Location']) . "</div>";
                    echo "</div>";

                    echo "<div class='job-footer'>";
                    if ($interval->invert == 1) {
                        if ($daysAgo == 0) {
                            echo "<span class='post-date'>Posted today</span>";
                        } elseif ($daysAgo == 1) {
                            echo "<span class='post-date'>Posted 1 day ago</span>";
                        } else {
                            echo "<span class='post-date'>Posted $daysAgo days ago</span>";
                        }
                    } else {
                        echo "<span class='post-date'>Posted on " . htmlspecialchars($job['PostDate']) . "</span>";
                    }
                    echo "<span class='apply-now'>Apply Now</span>";
                    echo "</div>";
                    echo "</a>";
                }
                echo '</div>';
            } else {
                echo '<div class="no-jobs">';
                echo '<h3>No jobs found</h3>';
                echo '<p>Try adjusting your search or check back later</p>';
                echo '</div>';
            }
            ?>
        </section>
    </main>
    
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-logo">
                    <span>JobFinder</span>
                </div>
                <div class="footer-links">
                    <a href="#">About Us</a>
                    <a href="#">Contact</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 JobFinder. All rights reserved. (Luqman Hakimi)</p>
            </div>
        </div>
    </footer>
</body>
</html>
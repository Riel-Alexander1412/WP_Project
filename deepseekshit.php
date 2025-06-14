<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listing | JobFinder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Listing.css">
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
                    <li><a href="Listing.php" class="active"><i class="fas fa-briefcase"></i> Browse Jobs</a></li>
                    <li><a href="Applied.php"><i class="fas fa-check-circle"></i> Applied Jobs</a></li>
                    <li><a href="Profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="Login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                $jobQuery = "SELECT * FROM job_listing WHERE tags = ?";
                $stmt = mysqli_prepare($conn, $jobQuery);
                mysqli_stmt_bind_param($stmt, "s", $selectedTag);
                mysqli_stmt_execute($stmt);
                $jobResult = mysqli_stmt_get_result($stmt);
            } else {
                $jobQuery = "SELECT * FROM job_listing";
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
                    echo "<div class='detail-item'><i class='fas fa-money-bill-wave'></i> RM " . htmlspecialchars($job['Salary']) . "</div>";
                    echo "<div class='detail-item'><i class='fas fa-map-marker-alt'></i> " . htmlspecialchars($job['Location']) . "</div>";
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
                    echo "<span class='apply-now'>Apply Now <i class='fas fa-arrow-right'></i></span>";
                    echo "</div>";
                    echo "</a>";
                }
                echo '</div>';
            } else {
                echo '<div class="no-jobs">';
                echo '<i class="fas fa-search"></i>';
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
                    <i class="fas fa-search-location"></i>
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
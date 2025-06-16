<?php
    session_start();
    include ('connection.php');
    //include ("control.php");
    
    if (isset($_GET['job_id'])) {
        $jobID = $_GET['job_id'];
        
        $query = "SELECT j.Position as jName, e.Name as cName, e.Address as eAddress 
                            FROM job_listing j
                            JOIN employer e ON j.EmployerID = e.Email 
                            WHERE j.ListingID = ? " ;
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $jobID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        function getTotalApplication($conn, $jobID){
            $sql = "SELECT COUNT(*) AS app_count FROM applied_jobs aj WHERE aj.jobID ='$jobID'";
            $result = $conn->query($sql);

            if ($result) {
                $row = $result->fetch_assoc();
                $totalAdmin = (int)$row['app_count'];
                return $totalAdmin;
            } else {
                error_log("Query error: " . $conn->error);
                return 0;
            }
        }
        $totalapp = getTotalApplication($conn, $jobID);
        
        $jName = "N/A";
        $cName = "N/A";
        $eAddress = "N/A";
        
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $jName = $row['jName'];
                $cName = $row['cName'];
                $eAddress = $row['eAddress'];
            }
        }
        
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applicants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/viewApplicants.css">
    <link rel="stylesheet" href="CSS/viewApplications.css">

</head>
<body>
    <header style="background:linear-gradient(135deg, #1a73e8, #0d47a1); border-radius:0;">
        <div class="header-content" style="width:100%;">
            <div class="logo">
                <img src="Assets/Image/logo_color.png" alt="text of logo" style="height:5vh;">
                Job<span>Finder</span>
            </div>
            <div class="employer-actions">
                <button class="btn btn-secondary" onclick="window.history.back();">
                    <i class="fas fa-arrow-left"></i> Back to Search
                </button>
            </div>
        </div>
    </header> 
    <div class="container">
        <header>
            <div class="job-info">
                <h1 class="job-title">
                    <i class="fas fa-briefcase"></i>
                    <?php echo $jName;?>
                </h1>
                <p><?php echo $cName ?>•<?php echo $eAddress?></p>
                
                <div class="job-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Posted: 2001-09-11</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-user-friends"></i>
                        <span><?php echo $totalapp;?> Applicants</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-stopwatch"></i>
                        <span>Closes: 2001-09-11</span>
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                <button class="action-btn primary">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </header>       
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(33, 150, 243, 0.1); color: var(--primary);">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalapp ?></div>
                    <div class="stat-title">Total Applicants</div>
                </div>
            </div>
        </div>      
        <div class="controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search applicants...">
            </div>
            
            <div class="filter-tabs">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-list"></i> All
                </button>
                <button class="filter-btn" data-filter="new">
                    <i class="fas fa-clock"></i> New
                </button>
                <button class="filter-btn" data-filter="reviewed">
                    <i class="fas fa-eye"></i> Reviewed
                </button>
                <button class="filter-btn" data-filter="rejected">
                    <i class="fas fa-times"></i> Rejected
                </button>
            </div>
        </div>       
        <div class="applicant-cards">
            <?php
                if (isset($_GET['job_id'])) {
                    $jobID = $_GET['job_id'];

                    // Prepare the query
                    $query = "SELECT aj.*, u.Name AS applicant_name, u.Email AS email, u.PhoneNum AS phone, u.Address AS location, u.Resume as resume 
                            FROM applied_jobs aj
                            JOIN user u ON aj.userEmail = u.Email 
                            WHERE aj.jobID = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "s", $jobID); // Use "s" for string
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    // Check if we have results
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Map database status to CSS classes
                            $statusClass = 'new';
                            ?>
                            <div class="applicant-card" data-status="<?= $statusClass ?>">
                                <div class="card-header">
                                    <div class="applicant-name">
                                        <i class="fas fa-user"></i>
                                        <?= htmlspecialchars($row['applicant_name']) ?>
                                    </div>
                                    <div class="applicant-email">
                                        <i class="fas fa-envelope"></i>
                                        <?= htmlspecialchars($row['UserEmail']) ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="info-content">
                                            <div class="info-label">Phone</div>
                                            <div class="info-value"><?= htmlspecialchars($row['phone'] ?? 'N/A') ?></div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="info-content">
                                            <div class="info-label">Location</div>
                                            <div class="info-value"><?= htmlspecialchars($row['location'] ?? 'Not specified') ?></div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <div class="info-content">
                                            <div class="info-label">Applied On</div>
                                            <div class="info-value"><?= htmlspecialchars($row['Date'] ?? 'Unknown date') ?></div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-file"></i>
                                        </div>
                                        <div class="info-content">
                                            <div class="info-label">Resume</div>
                                            <div class="info-value">
                                                <?php if (!empty($row['resume'])): ?>
                                                    <a href="<?= htmlspecialchars($row['resume']) ?>" target="_blank">
                                                        View Resume
                                                    </a>
                                                <?php else: ?>
                                                    No resume submitted
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <span class="status <?= $statusClass ?>">
                                        <?= ucfirst($statusClass) ?> Application
                                    </span>
                                    <div class="actions">
                                        <form action="viewApplicant.php" method="post">
                                            <input name="email" value="<?php echo $row['UserEmail'];?>" style="display:none;">
                                            <button class="action-btn secondary" type="submit">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        } 
                    else {
                            // No applicants found
                            echo '<div class="no-applicants">';
                            echo '<i class="fas fa-user-slash"></i>';
                            echo '<h3>No applicants found for this job</h3>';
                            echo '<p>There are currently no applications for this position.</p>';
                            echo '</div>';
                        }
                    } 
                else {
                        echo '<div class="error-message">';
                        echo '<i class="fas fa-exclamation-triangle"></i>';
                        echo '<h3>Job ID Missing</h3>';
                        echo '<p>Please specify a job ID in the URL.</p>';
                        echo '</div>';
                    }
            ?>
            
        </div>
    </div>
    <footer>
        <div class="footer-content">
            <div class="footer-info">
                <div class="footer-logo">Job<span>Finder</span></div>
                <p>Connecting exceptional slavery with forward-thinking black companies worldwide.</p>
            </div>
            
            <div class="footer-links">
                <div class="footer-column">
                    <h4>For Employers</h4>
                    <ul>
                        <li><a href="#">Post a Job</a></li>
                        <li><a href="#">Search Candidates</a></li>
                        <li><a href="#">Pricing Plans</a></li>
                        <li><a href="#">Employer Resources</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="employer_Profile.html">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
            </div>
        </div>       
        <div class="copyright">
            &copy; 2077 JobFinder. All rights reserved.
        </div>
    </footer> 
    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const applicantCards = document.querySelectorAll('.applicant-card');
            const searchInput = document.getElementById('searchInput');
            
            // Filter by status
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    
                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter cards
                    applicantCards.forEach(card => {
                        const status = card.dataset.status;
                        
                        if (filter === 'all') {
                            card.style.display = 'block';
                        } else {
                            card.style.display = status === filter ? 'block' : 'none';
                        }
                        
                        // Apply fade animation
                        card.style.animation = 'fadeIn 0.5s ease';
                    });
                });
            });
            
            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                applicantCards.forEach(card => {
                    const name = card.querySelector('.applicant-name').textContent.toLowerCase();
                    const email = card.querySelector('.applicant-email').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || email.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
            
            // Add animation to cards when they come into view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = "1";
                        entry.target.style.transform = "translateY(0)";
                    }
                });
            }, {
                threshold: 0.1
            });
            
            // Observe all cards
            document.querySelectorAll('.applicant-card').forEach(card => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(card);
            });
        });
    </script>
</body>
</html>
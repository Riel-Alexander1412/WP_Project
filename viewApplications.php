<?php
    session_start();
    include ('connection.php');
    //include ("control.php");
    
    if (isset($_GET['job_id'])) {
        $jobID = $_GET['job_id'];
        
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
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applicants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #0d47a1;
            --primary-light: #2196f3;
            --secondary: #ffc107;
            --light: #f5f7fa;
            --dark: #2c3e50;
            --success: #4CAF50;
            --warning: #FF9800;
            --danger: #F44336;
            --gray: #7f8c8d;
            --light-gray: #e0e0e0;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #e4e7f1);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .job-info {
            flex: 1;
            min-width: 300px;
        }

        .job-title {
            font-size: 28px;
            color: var(--dark);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .job-title i {
            color: var(--primary);
            font-size: 32px;
        }

        .job-meta {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-title {
            font-size: 14px;
            color: var(--gray);
        }

        .controls {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border-radius: 50px;
            border: 1px solid var(--light-gray);
            font-size: 16px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2);
        }

        .search-box i {
            position: absolute;
            left: 18px;
            top: 14px;
            color: var(--gray);
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border-radius: 50px;
            border: none;
            background: var(--light);
            color: var(--dark);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn.active, .filter-btn:hover {
            background: var(--primary);
            color: white;
        }

        .applicant-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .applicant-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .applicant-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            padding: 25px;
            background: linear-gradient(to right, var(--primary-light), var(--primary));
            color: white;
            position: relative;
        }

        .applicant-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .applicant-name i {
            font-size: 24px;
        }

        .applicant-email {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
            opacity: 0.9;
        }

        .card-body {
            padding: 25px;
            flex: 1;
        }

        .info-item {
            display: flex;
            margin-bottom: 16px;
            gap: 15px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
        }

        .card-footer {
            padding: 20px;
            border-top: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
        }

        .status.new {
            background: rgba(33, 150, 243, 0.15);
            color: var(--primary);
        }

        .status.reviewed {
            background: rgba(76, 175, 80, 0.15);
            color: var(--success);
        }

        .status.rejected {
            background: rgba(244, 67, 54, 0.15);
            color: var(--danger);
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
            font-size: 14px;
        }

        .action-btn.primary {
            background: var(--primary);
            color: white;
        }

        .action-btn.primary:hover {
            background: #0b3d91;
            transform: translateY(-2px);
        }

        .action-btn.secondary {
            background: white;
            color: var(--dark);
            border: 1px solid var(--light-gray);
        }

        .action-btn.secondary:hover {
            background: var(--light);
        }

        @media (max-width: 768px) {
            .job-title {
                font-size: 24px;
            }
            
            .applicant-cards {
                grid-template-columns: 1fr;
            }
            
            header {
                padding: 20px;
            }
            
            .controls {
                flex-direction: column;
            }
            
            .search-box {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="job-info">
                <h1 class="job-title">
                    <i class="fas fa-briefcase"></i>
                    Senior Frontend Developer
                </h1>
                <p>Tech Innovations Inc. • San Francisco, CA</p>
                
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
                                        <button class="action-btn secondary">
                                            <i class="fas fa-eye"></i> View
                                        </button>
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
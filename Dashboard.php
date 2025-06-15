<?php 
    include "connection.php";
    session_start();
    
    function getTotalAdmin($conn){
        $sql = "SELECT COUNT(*) AS admin_count FROM admin";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $totalAdmin = (int)$row['admin_count'];
            return $totalAdmin;
        } else {
            error_log("Query error: " . $conn->error);
            return 0;
        }
    }
    function getTotalUser($conn){
        $sql = "SELECT COUNT(*) AS user_count FROM user";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $totalUser = (int)$row['user_count'];
            return $totalUser;
        } else {
            error_log("Query error: " . $conn->error);
            return 0;
        }
    }
    function getTotalListing($conn){
        $sql = "SELECT COUNT(*) AS job_count FROM job_listing";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $totalJob = (int)$row['job_count'];
            return $totalJob;
        } else {
            error_log("Query error: " . $conn->error);
            return 0;
        }
    }
    function getActiveListing($conn){
        $sql = "SELECT COUNT(*) AS job_count FROM job_listing WHERE status = 'Active'";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $totalJob = (int)$row['job_count'];
            return $totalJob;
        } else {
            error_log("Query error: " . $conn->error);
            return 0;
        }
    }
    function getSuspendedListing($conn){
        $sql = "SELECT COUNT(*) AS job_count FROM job_listing WHERE status = 'Suspended'";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $totalJob = (int)$row['job_count'];
            return $totalJob;
        } else {
            error_log("Query error: " . $conn->error);
            return 0;
        }
    }

    function formatFancyDate($sql_date) {
        $date = new DateTime($sql_date);
        $now = new DateTime("now", $date->getTimezone());

        $today = $now->format('Y-m-d');
        $that_day = $date->format('Y-m-d');
        if ($that_day === $today) {
            $label = "Today";
        } 
        elseif ($that_day === $now->modify('-1 day')->format('Y-m-d')) {
            $label = "Yesterday";
        } 
        else {
            $label = $date->format("F j Y"); 
        }

        return $label . ", " . $date->format("g:iA");
    }
    $totalAdmin = getTotalAdmin($conn);
    $totalUser = getTotalUser($conn);
    $totalJobListing = getTotalListing($conn);
    $ActiveJobListing = getActiveListing($conn);
    $SuspendedJobListing = getSuspendedListing($conn);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (isset($data['action'])) {
            $action = $data['action'];
            switch ($action) {
                case 'suspendJob':
                    $jobId = $data['jobId'];
                    // Update database
                    $sql = "UPDATE job_listing SET Status = 'Suspended' WHERE ListingID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $jobId);
                    $stmt->execute();
                    // Send response
                    echo json_encode(['success' => true, 'message' => 'Job suspended']);
                    exit;
                case 'activateJob':
                    $jobId = $data['jobId'];
                    // Update database
                    $sql = "UPDATE job_listing SET Status = 'Active' WHERE ListingID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $jobId);
                    $stmt->execute();

                    // Send response
                    echo json_encode(['success' => true, 'message' => 'Job activated']);
                    exit;
                }
        }else{
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
        }
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
        }

        body {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            background-image: url(Assets/standardbg.jpg);
            background-size: contain;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        #main {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 1200px;
            width: 80vw;
            display: flex;
            max-height: 90vh;
            transition: all 0.3s ease;
        }
        
        /* Sidebar Styles */
        #sidebar {
            width: 30%;
            max-width: 300px;
            background: var(--dark);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 0 0 10px 0;
            transition: all 0.3s ease;
        }
        
        .profile-header {
            background: linear-gradient(to right, var(--primary-light), var(--primary));
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            margin: 0 auto 15px;
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: var(--primary);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .profile-pic:hover {
            transform: scale(1.05);
            border-color: var(--secondary);
        }
        
        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .profile-role {
            font-size: 14px;
            opacity: 0.9;
            background: rgba(0,0,0,0.2);
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
        }
        
        /* Navigation */
        #navbuttons {
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            flex: 1;
        }
        
        .option-btn {
            padding: 15px 25px;
            margin: 8px 15px;
            background: transparent;
            border: none;
            color: white;
            text-align: left;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .option-btn i {
            margin-right: 12px;
            width: 24px;
            text-align: center;
            font-size: 18px;
        }
        
        .option-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .option-btn.active {
            background: var(--primary);
            box-shadow: 0 4px 10px rgba(13, 71, 161, 0.3);
        }
        
        .option-btn.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--secondary);
        }
        
        .option-btn.logout {
            margin-top: auto;
            background: rgba(244, 67, 54, 0.1);
            color: #ff6b6b;
        }
        
        .option-btn.logout:hover {
            background: rgba(244, 67, 54, 0.2);
        }
        
        /* Content Area */
        #content {
            padding: 30px;
            background: var(--light);
            overflow-y: auto;
            width: inherit;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--light-gray);
        }
        
        .dashboard-title {
            font-size: 28px;
            color: var(--dark);
            display: flex;
            align-items: center;
        }
        
        .dashboard-title i {
            margin-right: 12px;
            color: var(--primary);
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
        
        .content-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 20px;
            color: var(--dark);
        }
        
        .btn {
            padding: 10px 20px;
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
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: #0b3d91;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 71, 161, 0.2);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        th {
            background-color: var(--light);
            color: var(--dark);
            font-weight: 600;
        }
        
        tr:hover {
            background-color: rgba(33, 150, 243, 0.05);
        }
        
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status.active {
            background: rgba(76, 175, 80, 0.1);
            color: var(--success);
        }
        
        .status.pending {
            background: rgba(255, 152, 0, 0.1);
            color: var(--warning);
        }
        
        .status.inactive {
            background: rgba(244, 67, 54, 0.1);
            color: var(--danger);
        }
        
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            background: transparent;
            border: none;
            cursor: pointer;
            margin: 0 3px;
            transition: all 0.2s;
        }
        
        .action-btn.edit {
            color: var(--primary);
            border: 1px solid var(--primary);
        }
        
        .action-btn.delete {
            color: var(--danger);
            border: 1px solid var(--danger);
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-btn {
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            background: var(--light-gray);
            color: var(--dark);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .filter-btn.active {
            background: var(--primary);
            color: white;
        }

        /* Job Cards */
        .job-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .job-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            display: block;
            flex-direction: column;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .card-header {
            padding: 15px;
            background: var(--light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--light-gray);
        }

        .card-header h3 {
            margin: 0;
            font-size: 18px;
            flex: 1;
        }

        .card-body {
            padding: 15px;
            flex: 1;
        }

        .card-body p {
            margin: 5px 0;
        }

        .card-actions {
            padding: 15px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid var(--light-gray);
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .no-jobs {
            text-align: center;
            padding: 20px;
            color: var(--gray);
            font-style: italic;
        }

        .status.ended {
            background: rgba(149, 117, 205, 0.1);
            color: #9575cd;
        }
        
        /* Responsive Design */
        @media (max-width: 900px) {
            #main {
                flex-direction: column;
            }
            
            #sidebar, #content {
                width: 100%;
                max-width: 100%;
            }
            
            #sidebar {
                padding: 0 0 10px 0;
            }
            
            #navbuttons {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                padding: 10px 0;
            }
            
            .option-btn {
                margin: 5px;
                padding: 12px 15px;
                font-size: 14px;
            }
            
            .option-btn.logout {
                margin-top: 0;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
        }
    </style>
</head>
<body>
    <div id="main">
        <div id="sidebar">
            <div class="profile-header">
                <div class="profile-pic">
                    <img id='profileimage' src='Datastore/Image/test.jpg' alt="Admin Profile"/>
                </div>
                <h2 class="profile-name"><?php echo $_SESSION['name'];?></h2>
                <div class="profile-role"><?php echo $_SESSION['email'];?></div>
            </div>           
            <div id="navbuttons">
                <button class="option-btn active" data-target="ManageAdmins">
                    <i class="fas fa-user-shield"></i> Manage Admin
                </button>
                <button class="option-btn" data-target="ManageUsers">
                    <i class="fas fa-users"></i> Manage Users
                </button>
                <button class="option-btn" data-target="ManageListing">
                    <i class="fas fa-briefcase"></i> Manage Job Listing
                </button>
                
            </div>
            <button class="option-btn logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
        <div id="content">
            <div id="ManageAdmins" class="content-section" style="display:none;">
                <div class="dashboard-header">
                    <h1 class="dashboard-title"><i class="fas fa-user-shield"></i> Admin Management</h1>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Admin
                    </button>
                </div>
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(13, 71, 161, 0.1); color: var(--primary);">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo $totalAdmin;?></div>
                            <div class="stat-title">Total Admins</div>
                        </div>
                    </div>
                </div>   
                <div class="content-section-data">
                    <div class="section-header">
                        <h2 class="section-title">Admin List</h2>
                        <div>
                            <input type="text" placeholder="Search admins..." style="padding: 10px; border-radius: 8px; border: 1px solid var(--light-gray);">
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Last Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                                $sql = "SELECT * FROM admin";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    $results = $result->fetch_all(MYSQLI_ASSOC);
                                    foreach($results as $admin){
                                        echo "<tr>";
                                        echo "<td>". $admin["Name"]."</td>";
                                        echo "<td>". $admin["Email"]."</td>";
                                        echo "<td>". formatFancyDate($admin["LastActive"])."</td>";
                                        echo "<td>";
                                        echo "<button class='action-btn edit'><i class='fas fa-edit'></i></button>";
                                        echo "<button class='action-btn delete'><i class='fas fa-trash'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }else{
                                    echo "No User Found";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="ManageUsers" class="content-section" style="display:none;">
                <div class="dashboard-header">
                    <h1 class="dashboard-title"><i class="fas fa-users"></i> User Management</h1>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New User
                    </button>
                </div>
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(13, 71, 161, 0.1); color: var(--primary);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo $totalUser;?></div>
                            <div class="stat-title">Total Users</div>
                        </div>
                    </div>
                </div>   
                <div class="content-section-data">
                    <div class="section-header">
                        <h2 class="section-title">User List</h2>
                        <div>
                            <input type="text" placeholder="Search Users..." style="padding: 10px; border-radius: 8px; border: 1px solid var(--light-gray);">
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Country</th>
                                <th>Birth Date</th>
                                <th>Gender</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                                $sql = "SELECT * FROM user";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    $results = $result->fetch_all(MYSQLI_ASSOC);
                                    foreach($results as $user){
                                        echo "<tr>";
                                        echo "<td>". $user["Name"]."</td>";
                                        echo "<td>". $user["Email"]."</td>";
                                        echo "<td>". $user["PhoneNum"]."</td>";
                                        echo "<td>". $user["Address"]."</td>";
                                        echo "<td>". $user["COO"]."</td>";
                                        echo "<td>". $user["DoB"]."</td>";
                                        echo "<td>". $user["Gender"]."</td>";
                                        echo "<td>";
                                        echo "<button class='action-btn edit'><i class='fas fa-edit'></i></button>";
                                        echo "<button class='action-btn delete'><i class='fas fa-trash'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }else{
                                    echo "No User Found";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>            
            <div id="ManageListing" class="content-section" style="display:none;">
                <div class="dashboard-header">
                    <h1 class="dashboard-title"><i class="fas fa-briefcase"></i> Job Management</h1>
                </div>
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(13, 71, 161, 0.1); color: var(--primary);">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo $totalJobListing;?></div>
                            <div class="stat-title">Total Jobs</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(76, 175, 80, 0.1); color: var(--success);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo $ActiveJobListing;?></div>
                            <div class="stat-title">Active Jobs</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(244, 67, 54, 0.1); color: var(--danger);">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo $SuspendedJobListing;?></div>
                            <div class="stat-title">Suspended Jobs</div>
                        </div>
                    </div>
                </div>   
                <div class="content-section-data">
                    <div class="section-header">
                        <h2 class="section-title">Job Listings</h2>
                        <div class="filter-tabs">
                            <button class="filter-btn active" data-filter="all">All</button>
                            <button class="filter-btn" data-filter="active">Active</button>
                            <button class="filter-btn" data-filter="suspended">Suspended</button>
                            <button class="filter-btn" data-filter="ended">Ended</button>
                        </div>
                    </div>
                    <div class="job-cards">
                        <?php
                            $sql = "SELECT * FROM job_listing";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $currentDate = new DateTime();
                        
                            if ($result->num_rows > 0) {
                                $results = $result->fetch_all(MYSQLI_ASSOC);

                                foreach ($results as $job) {
                                    $postedDate = new DateTime($job['PostDate']);
                                    $interval = $currentDate->diff($postedDate);
                                    $daysSincePosted = $interval->days;

                                    // Determine status based on days since posted and admin status
                                    $adminStatus = $job['Status']; // Actual status from database
                                    $isEnded = ($daysSincePosted > 30);

                                    // Override status to Ended if posted more than 30 days ago
                                    $displayStatus = $isEnded ? 'Ended' : $adminStatus;

                                    // Set CSS class based on status
                                    $statusClass = 'pending';
                                    if ($displayStatus === 'Active') {
                                        $statusClass = 'active';
                                    } elseif ($displayStatus === 'Suspended') {
                                        $statusClass = 'inactive';
                                    } elseif ($displayStatus === 'Ended') {
                                        $statusClass = 'ended';
                                    }
                                    
                                    //Create a card lol
                                    echo "<div class='job-card' data-status='$displayStatus'>";
                                    echo "<div class='card-header'>";
                                    echo "<h3>".$job['Position'] ."</h3>";
                                    echo "<span class='status $statusClass'>$displayStatus</span>";
                                    echo  "</div>";
                                    
                                    echo "<div class='card-body'>";
                                    echo "<p><strong>Company:</strong>".$job['EmployerID'] ?? 'N/A'."</p>";
                                    echo "<p><strong>Location:</strong>".$job['Location'] ?? 'N/A'."</p>";
                                    echo "<p><strong>Salary:</strong>".$job['Salary'] ?? 'N/A'."</p>";
                                    echo "</div>";
                                    
                                    if ($displayStatus == 'Active' && !$isEnded){
                                        echo "<button class='btn btn-warning suspend-job' data-id='".$job['ListingID']."'>";
                                        echo "<i class='fas fa-ban'></i> Suspend";
                                        echo "</button>";
                                    }
                                    elseif ($displayStatus == 'Suspended'){
                                        echo "<button class='btn btn-success activate-job' data-id='".$job['ListingID']."'>";
                                        echo "<i class='fas fa-check-circle'></i> Unsuspend";
                                        echo "</button>";
                                    }
                                    if($isEnded){
                                        echo "<span class='status ended'>Ended</span>";
                                    }
                                    echo "</div>";
                                }
                            }
                            else {
                                echo "<p class='no-jobs'>No job listings found</p>";
                            }
                            
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        function sendPostRequest(data, cardElement) {
            fetch("Dashboard.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log("Success:", data);
                location.reload();
            })
            .catch(error => {
                console.error("Error", error);
            });
        }
        function updateJobStatus(action, jobId, cardElement) {
            sendPostRequest({
                action: action,
                jobId: jobId
            }, cardElement);
        }   
        
        // Initialize event listeners
        document.querySelectorAll('.suspend-job').forEach(btn => {
            btn.addEventListener('click', function() {
                const jobId = this.dataset.id;
                const cardElement = this.closest('.job-card');
                if (confirm('Are you sure you want to suspend this job?')) {
                    updateJobStatus('suspendJob', jobId, cardElement);
                }
            });
        });

        document.querySelectorAll('.activate-job').forEach(btn => {
            btn.addEventListener('click', function() {
                const jobId = this.dataset.id;
                const cardElement = this.closest('.job-card');
                if (confirm('Are you sure you want to activate this job?')) {
                    updateJobStatus('activateJob', jobId, cardElement);
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const optionButtons = document.querySelectorAll('.option-btn');
            const contentSections = document.querySelectorAll('.content-section');
            
            function switchTab(target) {
                // Update active button
                optionButtons.forEach(btn => btn.classList.remove('active'));
                document.querySelector(`.option-btn[data-target="${target}"]`).classList.add('active');
                
                // Show target section
                contentSections.forEach(section => section.style.display = 'none');
                document.getElementById(target).style.display = 'block';
                
                // Update URL hash
                window.location.hash = target;
            }
            
            // Handle initial page load based on URL hash
            function handleInitialTab() {
                const hash = window.location.hash.substring(1);
                const validTargets = ['ManageAdmins', 'ManageUsers', 'ManageListing'];
                
                if (validTargets.includes(hash)) {
                    switchTab(hash);
                } else {
                    // Default to ManageAdmins if no valid hash
                    switchTab('ManageAdmins');
                }
            }
            
            // Set up button click handlers
            optionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');                   
                    if (this.classList.contains('logout')) {
                        // Logout functionality remains the same
                        const link = document.createElement('a');
                        link.href = "Logout.php";
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        alert('Logging out...');
                        return;
                    }
                    switchTab(target);
                });
            });
            
            // Handle initial page load
            handleInitialTab();
            
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
            
            // Observe all stat cards
            document.querySelectorAll('.stat-card').forEach(card => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(card);
            });
        });
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active button
                document.querySelectorAll('.filter-btn').forEach(b => 
                    b.classList.remove('active'));
                    this.classList.add('active');
                    const filter = this.dataset.filter;
                    document.querySelectorAll('.job-card').forEach(card => {
                        const status = card.dataset.status;
                        if (filter === 'all') {
                            card.style.display = 'block';
                        } else if(filter === "active" && status == "Active"){
                            card.style.display = 'block';
                        } else if(filter === "suspended" && status == "Suspended"){
                            card.style.display = 'block';
                        }else if(filter === "ended" && status == "Ended"){
                            card.style.display = 'block';
                        }else{
                            card.style.display = card.dataset.status === filter ? 'block' : 'none';
                        }
                });
            });
        });
    </script>
</body>
</html>
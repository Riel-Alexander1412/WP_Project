<?php 
include "connection.php";
$user = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $user = $results[0];
    }else{
        echo "No User Found";
    }
    
}else{
    header("Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ");
    die();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
        }
        
        .logo span {
            color: #ffc107;
        }
        
        .employer-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: #ffc107;
            color: #0d47a1;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #ffb300;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: transparent;
            border: 2px solid white;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .profile-container {
            display: flex;
            gap: 30px;
            margin-top: 30px;
        }
        
        .profile-sidebar {
            flex: 1;
            max-width: 300px;
        }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        
        .profile-header {
            background: linear-gradient(to right, #2196f3, #0d47a1);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            margin: 0 auto 15px;
            background-color: #e3f2fd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: #0d47a1;
            overflow: hidden;
        }
        .profile-pic img{
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .profile-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .profile-title {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .profile-contact {
            padding: 20px;
        }
        
        .contact-item {
            display: flex;
            margin-bottom: 15px;
            align-items: flex-start;
        }
        
        .contact-icon {
            width: 40px;
            height: 40px;
            background-color: #e3f2fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d47a1;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .contact-details h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .contact-details p {
            font-size: 16px;
            font-weight: 500;
        }
        
        .profile-main {
            flex: 2;
        }
        
        .section {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        
        .section-title {
            font-size: 22px;
            color: #0d47a1;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e3f2fd;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            background-color: #e3f2fd;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d47a1;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .info-item h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .info-item p {
            font-size: 16px;
            font-weight: 500;
        }
        
        .education-item {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eee;
        }
        
        .education-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .education-degree {
            font-size: 18px;
            font-weight: 600;
            color: #0d47a1;
            margin-bottom: 5px;
        }
        
        .education-school {
            font-size: 16px;
            margin-bottom: 10px;
            color: #2196f3;
        }
        
        .education-duration {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .education-description {
            line-height: 1.7;
        }
        
        .description-text {
            line-height: 1.8;
            font-size: 16px;
        }
        
        .resume-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        
        .resume-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .resume-preview {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            height: 600px;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .resume-preview iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .resume-placeholder {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        
        .resume-placeholder i {
            font-size: 50px;
            color: #2196f3;
            margin-bottom: 15px;
        }
        
        .resume-placeholder h3 {
            margin-bottom: 10px;
            color: #0d47a1;
        }
        
        footer {
            background-color: #0d47a1;
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .footer-logo span {
            color: #ffc107;
        }
        
        .footer-links {
            display: flex;
            gap: 30px;
        }
        
        .footer-column h4 {
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: #e3f2fd;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: #ffc107;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
            color: #e3f2fd;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }
            
            .profile-sidebar {
                max-width: 100%;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .resume-actions {
                flex-direction: column;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 30px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">Talent<span>Connect</span></div>
            <div class="employer-actions">
                <button class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Search
                </button>
                <button class="btn btn-primary">
                    <i class="fas fa-download"></i> Download Full Profile
                </button>
            </div>
        </div>
    </header> 
    <div class="container">
        <div class="profile-container">
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-pic">
                           <?php 
                            if($user['Image']){
                                //echo "<img src='" . htmlspecialchars($user['Image']) . "/>";
                                echo "<img id='profileimage' src='".$user['Image']."'/>";
                            }else{
                                echo "<i class='fas fa-user'></i>";
                            }
                            ?>
                        </div>
                        <h1 class="profile-name"><?php echo $user["Name"];?></h1>
                    </div>
                    
                    <div class="profile-contact">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Email</h3>
                                <p><?php echo $user["Email"];?></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Phone</h3>
                                <p><?php echo $user["PhoneNum"];?></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Location</h3>
                                <p>San Francisco, CA, USA</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-user-graduate"></i>
                        Highest Education
                    </h2>
                    <div class="education-item">
                        <div class="education-degree">Master of Computer Science</div>
                        <div class="education-school">Stanford University</div>
                        <div class="education-duration">2015 - 2017</div>
                    </div>
                </div>
            </div>
            
            <div class="profile-main">
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Personal Information
                    </h2>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <h3>Full Name</h3>
                            <p><?php echo $user["Name"];?></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Date of Birth</h3>
                            <p><?php echo $user["DoB"];?></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Gender</h3>
                            <p><?php echo $user["Gender"];?></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Country</h3>
                            <p><?php echo $user["COO"];?></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Address</h3>
                            <p><?php echo $user["Address"];?></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Nationality</h3>
                            <p>American</p>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        Education
                    </h2>
                    
                    <div class="education-item">
                        <div class="education-degree">Bachelor of Science in Computer Engineering</div>
                        <div class="education-school">University of California, Berkeley</div>
                        <div class="education-duration">2011 - 2015</div>
                        <div class="education-description">
                            <p>Fought Gojo Satoru and Toji Fushiguro. Dean's List all semesters.</p>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-user-edit"></i>
                        <span>Why You Should Hire Me</span>
                    </h2>
                    
                    <div class="description-text">
                        <pre><?php echo $user["UniFeat"];?></pre>
                    </div>
                </div>
                
                <div class="resume-section">
                    <h2 class="section-title">
                        <i class="fas fa-file-pdf"></i>
                        Professional Resume
                    </h2>
                    
                    <div class="resume-actions">
                        <button id="ViewBtn" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Open New Tab
                        </button>
                        <button id="DownloadBtn" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Resume
                        </button>
                        <button id="PrintBtn" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print Resume
                        </button>
                    </div>
                    
                    <div class="resume-preview">
                       <iframe title="Resume" src="<?php echo $user['Resume'];?>"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>    
    <footer>
        <div class="footer-content">
            <div class="footer-info">
                <div class="footer-logo">Talent<span>Connect</span></div>
                <p>Connecting exceptional talent with forward-thinking companies worldwide.</p>
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
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            &copy; 2023 TalentConnect. All rights reserved.
        </div>
    </footer>   
    <script>
        const viewBtn = document.getElementById('ViewBtn');
        const downloadBtn = document.getElementById('DownloadBtn');
        const printBtn = document.getElementById('PrintBtn');
        
        viewBtn.addEventListener('click', function() {
            window.open("<?php echo $user['Resume'];?>", '_blank');
        });
        
        downloadBtn.addEventListener('click', function() {
            const link = document.createElement('a');
            link.href = "<?php echo $user['Resume'];?>";
            link.download = '<?php echo $user['Name']."_RESUME";?>.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
        
        printBtn.addEventListener('click', function() {
            const printWindow = window.open('Datastore/File/Resume.pdf', '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        });
        
        //Loading animation
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.profile-card, .section');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            });
            
        });
    </script>
</body>
</html>
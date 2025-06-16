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
    
    if ($result->num_rows > 0) {
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
    <link rel="stylesheet" href="CSS/viewApplicants.css">
</head>
<body>
    <header>
        <div class="header-content">
            
            <div class="logo">
                <img src="Assets/Image/logo_color.png" alt="text of logo" style="height:5vh;">
                Job<span>Finder</span>
            </div>
            <div class="employer-actions">
                <button class="btn btn-secondary" onclick="window.history.back();">
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
                                <p><?php echo $user["Address"];?></p>
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
                        <div class="education-degree"><?php echo $user["HiEdu"];?></div>
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
                        <li><a href="">About Us</a></li>
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
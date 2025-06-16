<?php
    include "connection.php";
    session_start();

    $login_error = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = $_POST["email"];   
        $password = $_POST["password"];
        $sql = "SELECT 'admin' AS role, password, email, name FROM admin WHERE email = ? UNION ALL SELECT 'user' AS role, password, email, name FROM user WHERE email = ? UNION ALL SELECT 'employer' AS role, password, email, name FROM employer WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $email, $email);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $results = $result->fetch_all(MYSQLI_ASSOC);
            $authenticated = false;
            $user_data = null;

            foreach ($results as $user) {
                if (password_verify($password,$user['password'])) {
                    $authenticated = true;
                    $user_data = $user;
                    break;
                }
            }

            if ($authenticated) {
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $user_data['email'];
                $_SESSION['name'] = $user_data['name'];
                $_SESSION['role'] = $user_data['role'];
                
                //Redirect to user specific sites
                if($_SESSION['role'] === "user"){
                    header("Location: Listing.php");
                    die();
                }else if($_SESSION['role'] === "employer"){
                    header("Location: ManageListings.php");
                    die();
                }else if($_SESSION['role'] === "admin"){
                    header("Location: Dashboard.php");
                    die();
                }else{
                    header("Location: Login.php");
                    die();
                }
                exit;
            } 
            else {
                $login_error = "Invalid password";
            }
        }
        else {
            $login_error = "No account found with that email";
        }
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/viewApplicants.css">
    <link rel="stylesheet" href="CSS/Login.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="Assets/Image/logo_color.png" alt="text of logo" style="height:5vh;">
                Job<span style="color:yellow;">Finder</span>
            </div>
        </div>
    </header> 


    <div class="container">
        <div class="login-container">
           
            <div class="login-box">
                <h2>Login</h2>
                <?php if ($login_error): ?>
                <div class="error-message"><?php echo $login_error; ?></div>
                <?php endif; ?>
                <form action="Login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email">
                    </div>                 
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>                    
                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <div class="forgot-password">
                            <a href="/forgot-password">Forgot password?</a>
                        </div>
                    </div>                    
                    <button type="submit" class="login-button">Login</button>                   
                    <div class="register-link">
                        <p>Don't have an account? <a href="Register.html">Register here</a></p>
                    </div>                    
                    <div class="social-login">
                        <p>Or login with</p>
                        <div class="social-icons">
                            <div class="social-icon google">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path><path d="M12 8v8m4-4H8"></path></svg>
                            </div>
                            <div class="social-icon linkedin">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                            </div>
                            <div class="social-icon facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                            </div>
                        </div>
                    </div>
                </form>
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
</body>
</html>
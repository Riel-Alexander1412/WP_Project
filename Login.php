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
                    header("Location: ManageListing.php");
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
        }
        
        .logo span {
            color: #3498db;
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
        }
        
        .login-box {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        
        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-group input:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 8px;
        }
        
        .forgot-password a {
            color: #3498db;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .login-button:hover {
            background-color: #2980b9;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .social-login {
            margin-top: 30px;
            text-align: center;
        }
        
        .social-login p {
            margin-bottom: 15px;
            color: #7f8c8d;
            position: relative;
        }
        
        .social-login p::before,
        .social-login p::after {
            content: "";
            display: inline-block;
            width: 30%;
            height: 1px;
            background-color: #ddd;
            position: absolute;
            top: 50%;
        }
        
        .social-login p::before {
            left: 0;
        }
        
        .social-login p::after {
            right: 0;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .social-icon:hover {
            transform: translateY(-3px);
        }
        
        .google {
            background-color: #db4437;
        }
        
        .linkedin {
            background-color: #0077b5;
        }
        
        .facebook {
            background-color: #4267b2;
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(231, 76, 60, 0.3);
            border-radius: 5px;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                Job<span>Finder</span>
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
        <div class="container">
            <p>&copy; 2077 Cyberpunk Edgerunner. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php
@include 'config.php';
session_start();

if(isset($_POST['submit'])){
    // Verify reCAPTCHA first
    $recaptcha_secret = "6LentP8qAAAAAGeWnYe25HH_X5RedDofHPV2Q30p"; // Updated secret key
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    if(empty($recaptcha_response)) {
        $message[] = 'Please complete the reCAPTCHA verification!';
    } else {
        $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$recaptcha_secret.'&response='.$recaptcha_response);
        $response_data = json_decode($verify_response);
        
        if(!$response_data->success){
            $message[] = 'reCAPTCHA verification failed! Please try again.';
        } else {
            // Proceed with login validation
            $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
            $email = mysqli_real_escape_string($conn, $filter_email);
            $filter_pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
            $pass = mysqli_real_escape_string($conn, md5($filter_pass));

            $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

            if(mysqli_num_rows($select_users) > 0){
                $row = mysqli_fetch_assoc($select_users);
                
                // Store user data in session
                if($row['user_type'] == 'admin'){
                    $_SESSION['admin_name'] = $row['name'];
                    $_SESSION['admin_email'] = $row['email'];
                    $_SESSION['admin_id'] = $row['id'];
                    $redirect_url = 'Dashboard.php';
                    $button_text = 'Go to Dashboard'; // Text for admin
                }elseif($row['user_type'] == 'user'){
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['user_email'] = $row['email'];
                    $_SESSION['user_id'] = $row['id'];
                    $redirect_url = 'home.php';
                    $button_text = 'Go to Home Page'; // Text for user
                }
                
                // Show success animation
                echo '<!DOCTYPE html>
                <html lang="en">
                <head>
                    
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Login Success</title>
                    <style>
                        .success-overlay {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0, 0, 0, 0.5);
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            z-index: 1000;
                            animation: fadeIn 0.3s ease-out;
                        }
                        .success-box {
                            background: white;
                            padding: 30px;
                            border-radius: 10px;
                            text-align: center;
                            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                            animation: slideIn 0.5s ease-out;
                        }
                        .checkmark {
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            margin: 0 auto 20px;
                            animation: scaleIn 0.5s ease-out 0.5s both;
                        }
                        .checkmark__circle {
                            stroke-dasharray: 166;
                            stroke-dashoffset: 166;
                            stroke-width: 2;
                            stroke: #27ae60;
                            fill: none;
                            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
                        }
                        .checkmark__check {
                            transform-origin: 50% 50%;
                            stroke-dasharray: 48;
                            stroke-dashoffset: 48;
                            stroke: #27ae60;
                            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
                        }
                        .success-box h2 {
                            color: #27ae60;
                            margin: 10px 0;
                            font-size: 24px;
                            opacity: 0;
                            animation: fadeIn 0.5s ease-out 1s forwards;
                        }
                        .success-box p {
                            color: #666;
                            margin: 10px 0 20px;
                            opacity: 0;
                            animation: fadeIn 0.5s ease-out 1.2s forwards;
                        }
                        .success-btn {
                            background: #27ae60;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 5px;
                            cursor: pointer;
                            opacity: 0;
                            animation: fadeIn 0.5s ease-out 1.4s forwards;
                        }
                        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
                        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
                        @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
                        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
                    </style>
                </head>
                <body>
                    <div class="success-overlay">
                        <div class="success-box">
                            <!-- ... [existing SVG code] ... -->
                            <svg class="checkmark" viewBox="0 0 52 52">
                                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                            </svg>
                            <h2>Welcome Back, '.htmlspecialchars($row['name']).'!</h2>
                            <p>Login successful</p>
                            <button class="success-btn" onclick="window.location.href=\''.$redirect_url.'\'">
                                '.$button_text.' <!-- Dynamic button text -->
                            </button>
                        </div>
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.href = "'.$redirect_url.'";
                        }, 5000);
                    </script>
                </body>
                </html>';
                exit();
            }
            else{
                $message[] = 'Incorrect email or password!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="images/pharmalogo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Login</title>
    <link rel="stylesheet" href="css/logs.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Rubik", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            background-size: cover;
            background-position: center;
        }

        .wrapper {
            width: 420px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #00bcd4;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(0, 188, 212, 0.3);
            color: #333;
            border-radius: 10px;
            padding: 30px 40px;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
            color: #00bcd4;
            margin-bottom: 20px;
        }

        .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            margin: 20px 0;
        }

        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: 2px solid #00bcd4;
            border-radius: 25px;
            font-size: 16px;
            color: #333;
            padding: 20px 45px 20px 20px;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-box input:focus {
            border-color: #0097a7;
            box-shadow: 0 0 10px rgba(0, 188, 212, 0.2);
        }

        .input-box input::placeholder {
            color: #777;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #00bcd4;
            transition: all 0.3s ease;
            cursor: pointer; /* Add cursor pointer for better UX */
        }

        .input-box input:focus + i {
            color: #0097a7;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14.5px;
            margin: 15px 0;
        }

        .remember-forgot label {
            display: flex;
            align-items: center;
        }

        .remember-forgot label input {
            accent-color: #00bcd4;
            margin-right: 5px;
        }

        .remember-forgot a {
            color: #00bcd4;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .remember-forgot a:hover {
            color: #0097a7;
            text-decoration: underline;
        }

        .recaptcha-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            transform: scale(0.9);
            transform-origin: center;
        }

        .btn {
            width: 100%;
            height: 45px;
            background: #00bcd4;
            border: none;
            outline: none;
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0, 188, 212, 0.3);
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #0097a7;
            box-shadow: 0 0 15px rgba(0, 188, 212, 0.5);
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #00bcd4;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: #0097a7;
            text-decoration: underline;
        }

        /* Message styling */
        .message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { transform: translate(-50%, -100%); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
    </style>
</head>
<body>
    <?php
    if(!empty($message)){
        foreach($message as $msg){
            echo '
            <div class="message">
            <span>'.$msg.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
            ';
        }
    }
    ?>
    
    <div class="wrapper">
        <form action="" method="post">
            <img src="images/pharmar.png" alt="Pharmacy Logo" style="height: auto; width: 70%">
            <h1 style="text-align: center; margin-bottom: 30px; color: #333">Pharmacy Login</h1>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div class="input-box">
                <input type="password" name="pass" id="password" placeholder="Password" required>
                <i class="fa-solid fa-lock" id="togglePassword"></i>
            </div>
            <div style="display: flex; justify-content: space-between; margin: 15px 0">
                <label><input type="checkbox"> Remember Me</label>
                <a href="forgot_passwords.php" style="text-decoration: none; color: #27ae60">Forgot Password?</a>
            </div>
            
            <div class="recaptcha-container">
                <div class="g-recaptcha" data-sitekey="6LentP8qAAAAADNT5KZHb15j9fECK-OfgtfclBfi"></div>
            </div>
            
            <button type="submit" name="submit" class="btn">Login Now</button>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php" style="color: #27ae60">Register</a></p>
            </div>
        </form>
    </div>

    <script>
        // Remove error messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(msg => msg.remove());
        }, 5000);

        // Password toggle functionality
        const passwordField = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        // Function to toggle password visibility
        togglePassword.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                togglePassword.classList.remove('fa-lock');
                togglePassword.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                togglePassword.classList.remove('fa-eye');
                togglePassword.classList.add('fa-lock');
            }
        });
    </script>
</body>
</html>
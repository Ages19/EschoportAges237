<?php
@include 'config.php';

if(isset($_POST['submit'])){
    $filter_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $name = mysqli_real_escape_string($conn, $filter_name);
    $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $email = mysqli_real_escape_string($conn, $filter_email);
    $filter_phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $phone = mysqli_real_escape_string($conn, $filter_phone);
    $filter_pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
    $filter_cpass = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);

    // Check username length
    if(strlen($name) < 3){
        $message[] = 'Username must be at least 3 characters long!';
    }
    // Check email format
   elseif(!preg_match('/^[a-zA-Z0-9+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/', $email)){
      $message[] = 'Please enter a valid email address!';
   }
    // Check phone number format
    elseif(!preg_match('/^\+?[0-9]{10,15}$/', $phone)){
        $message[] = 'Please enter a valid phone number!';
    }
    // Check password length
    elseif(strlen($_POST['pass']) < 8){
        $message[] = 'Password must be at least 8 characters long!';
    }
    else{
        $pass = mysqli_real_escape_string($conn, md5($filter_pass));
        $cpass = mysqli_real_escape_string($conn, md5($filter_cpass));

        $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');

        if(mysqli_num_rows($select_users) > 0){
            $message[] = 'User already exists!';
        }else{
            if($pass != $cpass){
                $message[] = 'Confirm password does not match!';
            }else{
                mysqli_query($conn, "INSERT INTO `users`(name, email, phone, password) VALUES('$name', '$email', '$phone', '$pass')") or die('query failed');
                $message[] = 'Registered successfully!';
                header('location:login.php');
                exit();
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/logs.css">

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
   background: linear-gradient(135deg, #e0f7fa, #80deea); /* Light blue gradient */
   background-size: cover;
   background-position: center;
}

.message {
   position: fixed;
   top: 0;
   margin: 0 auto;
   max-width: 1200px;
   background-color: #fff;
   padding: 10px;
   display: flex;
   align-items: center;
   justify-content: space-between;
   z-index: 10000;
   gap: 1.5rem;
   border-radius: 5px;
   box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.message span {
   font-size: 1rem;
   color: #333;
}

.message i {
   cursor: pointer;
   color: #ff4757; /* Red color for close icon */
   font-size: 1.2rem;
}

.message i:hover {
   transform: rotate(90deg);
}

.wrapper {
   width: 420px;
   background: rgba(255, 255, 255, 0.9); /* Semi-transparent white */
   border: 2px solid #00bcd4; /* Cyan border */
   backdrop-filter: blur(10px);
   box-shadow: 0 0 20px rgba(0, 188, 212, 0.3); /* Cyan shadow */
   color: #333;
   border-radius: 10px;
   padding: 30px 40px;
}

.wrapper h1 {
   font-size: 36px;
   text-align: center;
   color: #00bcd4; /* Cyan color for heading */
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
   border: 2px solid #00bcd4; /* Cyan border */
   border-radius: 25px;
   font-size: 16px;
   color: #333;
   padding: 20px 45px 20px 20px;
   outline: none;
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
   color: #00bcd4; /* Cyan color for icons */
   cursor: pointer; /* Add cursor pointer for better UX */
}

.remember-forgot {
   display: flex;
   justify-content: center;
   align-items: center;
   font-size: 14.5px;
   margin: 10px 0 15px;
}

.remember-forgot label input {
   accent-color: #00bcd4; /* Cyan color for checkbox */
   margin-right: 10px; /* Space between checkbox and text */
}

.remember-forgot a {
   color: #00bcd4; /* Cyan color for links */
   text-decoration: none;
}

.remember-forgot a:hover {
   text-decoration: underline;
}

.btn {
   width: 100%;
   height: 45px;
   background: #00bcd4; /* Cyan background for button */
   border: none;
   outline: none;
   border-radius: 25px;
   box-shadow: 0 0 10px rgba(0, 188, 212, 0.3); /* Cyan shadow */
   cursor: pointer;
   font-size: 16px;
   color: #fff;
   font-weight: 600;
   background-color: #00bcd4;
}

.btn:hover {
   background: #0097a7; /* Darker cyan on hover */
}

.register-link {
   font-size: 14.5px;
   text-align: center;
   margin: 20px 0 15px;
}

.register-link p a {
   color: #00bcd4; /* Cyan color for register link */
   text-decoration: none;
   font-weight: 600;
}

.register-link p a:hover {
   text-decoration: underline;
}

</style>
</head>
<body>

<?php
if(isset($message)){
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
        <h1>Register</h1>
        <div class="input-box">
            <input type="text" name="name" placeholder="Username" required>
            <i class="fa-solid fa-user"></i>
        </div>

        <div class="input-box">
            <input type="email" name="email" placeholder="Email" required>
            <i class="fa-solid fa-envelope"></i>
        </div>
        
        <div class="input-box">
            <input type="tel" name="phone" placeholder="Phone Number (with country code)" required>
            <i class="fa-solid fa-phone"></i>
        </div>

        <div class="input-box">
            <input type="password" name="pass" id="password" placeholder="Enter your Password" required>
            <i class="fa-solid fa-lock" id="togglePassword"></i>
        </div>

        <div class="input-box">
            <input type="password" name="cpass" id="confirmPassword" placeholder="Confirm Password" required>
            <i class="fa-solid fa-lock" id="toggleConfirmPassword"></i>
        </div>

        <div class="remember-forgot">
            <label><input type="checkbox" required>I agree with the <a href="terms.html">Terms and conditions</a></label>
        </div>

        <input type="submit" name="submit" value="Register Now" class="btn">

        <div class="register-link">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </form>
</div>

<script>
    // Get references to the password fields and toggle icons
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirmPassword');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    // Function to toggle password visibility
    function togglePasswordVisibility(passwordField, toggleIcon) {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-lock');
            toggleIcon.classList.add('fa-eye');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-lock');
        }
    }

    // Add click event listeners to the toggle icons
    togglePassword.addEventListener('click', function() {
        togglePasswordVisibility(passwordField, togglePassword);
    });

    toggleConfirmPassword.addEventListener('click', function() {
        togglePasswordVisibility(confirmPasswordField, toggleConfirmPassword);
    });
</script>

</body>
</html>
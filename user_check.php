<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['temp_appointment'])) {
    header('Location: ChannelingForm.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account Check - Care Compass Hospitals</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    <div class="form-container">
        <h1>Almost there!</h1>
        <p>To complete your appointment booking, please:</p>
        
        <div class="options-container">
            <div class="option-box">
                <h2>Existing User?</h2>
                <p>Login to your account to continue</p>
                <a href="Login.php?redirect=process_appointment" class="btn btn-primary">Login</a>
            </div>
            
            <div class="option-box">
                <h2>New User?</h2>
                <p>Create an account to manage your appointments</p>
                <a href="Register.php?redirect=process_appointment" class="btn btn-secondary">Create Account</a>
            </div>
        </div>
    </div>
</body>
</html>
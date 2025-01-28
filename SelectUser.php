<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select User Role - Care Compass Hospitals</title>
    <link rel="stylesheet" href="SelectUser.css">
    <style>
       
    </style>
</head>

<body>
    <div>
        <header>
            <nav class="navbar">
                <div class="logo">
                    <img src="assets/logo.png" alt="Care Compass Logo">
                </div>
                <ul class="nav-links">
                    <li><a href="#services">Services</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <!-- <li><a href="SelectUser.php" id="Login-btn" class="btn-primary">Login</a></li> -->
                </ul>
            </nav>
        </header>
    </div>
    <div class="role-container">
        <h1>Welcome to Care Compass Hospitals</h1>
        <p>Please select your role to continue</p>

        <div class="role-buttons">
            <button onclick="window.location.href='AdminLogin.php'" class="role-button admin-btn">Administrator</button>
            <button onclick="window.location.href='StaffLogin.php'" class="role-button staff-btn">Staff</button>
            <button onclick="window.location.href='Login.php'" class="role-button patient-btn">Patient</button>
        </div>
    </div>
</body>

</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... existing login logic ...

    if ($login_successful) {
        if (isset($_GET['redirect']) && $_GET['redirect'] === 'lab_appointment') {
            header('Location: process_lab_appointment.php');
        } else {
            header('Location: Patient.php');
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Care Compass Hospitals</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="login-container">
        <form id="login-form" action="Auth.php" method="POST">
            <h2>Login to Care Compass</h2>
            <div id="error-message" class="error-message" style="display: none; color: red; margin-bottom: 10px;"></div>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-primary">Login</button>
            <p class="signup-link">Don't have an account? <a href="SignUp.php">Sign Up</a></p>
        </form>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const errorDiv = document.getElementById('error-message');

            fetch('Auth.php', {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = data.redirect;
                    } else {
                        errorDiv.textContent = data.message;
                        errorDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorDiv.textContent = 'An error occurred. Please try again.';
                    errorDiv.style.display = 'block';
                });
        });
    </script>
</body>

</html>
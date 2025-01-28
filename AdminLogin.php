<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Care Compass Hospitals</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <form id="admin-login-form" action="AdminAuth.php" method="POST">
            <h2>Administrator Login</h2>
            <div id="error-message" class="error-message" style="display: none; color: red; margin-bottom: 10px;"></div>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-primary">Login</button>
            <p class="back-link"><a href="SelectUser.php">← Back to Role Selection</a></p>
            <!-- <p class="signup-link">Don't have an account? <a href="AdminSignUp.php">Sign Up</a></p> -->

        </form>
    </div>

    <script>
        document.getElementById('admin-login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const errorDiv = document.getElementById('error-message');
            
            fetch('AdminAuth.php', {
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
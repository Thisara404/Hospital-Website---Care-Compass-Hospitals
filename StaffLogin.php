<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - Care Compass Hospitals</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <form id="staff-login-form" action="StaffAuth.php" method="POST">
            <h2>Staff Login</h2>
            <div id="error-message" class="error-message" style="display: none; color: red; margin-bottom: 10px;"></div>
            <input type="text" name="staff_id" placeholder="Staff ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-primary">Login</button>
            <p class="back-link"><a href="SelectUser.php">← Back to Role Selection</a></p>
        </form>
    </div>

    <script>
        document.getElementById('staff-login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const errorDiv = document.getElementById('error-message');
            
            fetch('StaffAuth.php', {
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
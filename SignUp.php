<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Care Compass Hospitals</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="signup-container">
        <form id="signup-form" action="Register.php" method="POST">
            <h2>Create Your Account</h2>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="tel" name="phone" placeholder="Phone Number">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" class="btn-primary">Sign Up</button>
            <p class="login-link">Already have an account? <a href="Login.php">Login</a></p>
        </form>
    </div>

    <script>
        document.getElementById('signup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic client-side password validation
            const password = this.password.value;
            const confirmPassword = this.confirm_password.value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            fetch('Register.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Registration successful! Please login.');
                    window.location.href = 'Login.php';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    </script>
</body>
</html>
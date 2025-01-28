<?php
session_start();
require_once 'db_connection.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: AdminDashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => ''];
    
    // Validate input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    // Validation checks
    if (!$username || !$password || !$confirm_password || !$full_name || !$email) {
        $response['message'] = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $response['message'] = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $response['message'] = 'Password must be at least 8 characters long';
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['message'] = 'Username or email already exists';
        } else {
            // Insert new admin
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO admins (username, password, full_name, email) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $username, $hashed_password, $full_name, $email);
            
            if ($insert_stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Account created successfully';
                $response['redirect'] = 'AdminLogin.php';
            } else {
                $response['message'] = 'Error creating account';
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up - Care Compass Hospitals</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .signup-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .password-requirements {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <form id="admin-signup-form" method="POST">
            <h2>Administrator Sign Up</h2>
            <div id="error-message" class="error-message"></div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <div class="password-requirements">
                    Password must be at least 8 characters long
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn-primary">Sign Up</button>
            <p class="back-link"><a href="AdminLogin.php">← Back to Login</a></p>
        </form>
    </div>

    <script>
        document.getElementById('admin-signup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const errorDiv = document.getElementById('error-message');
            
            // Basic client-side validation
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                errorDiv.textContent = 'Passwords do not match';
                errorDiv.style.display = 'block';
                return;
            }
            
            if (password.length < 8) {
                errorDiv.textContent = 'Password must be at least 8 characters long';
                errorDiv.style.display = 'block';
                return;
            }
            
            fetch('AdminSignUp.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Account created successfully! Redirecting to login page...');
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
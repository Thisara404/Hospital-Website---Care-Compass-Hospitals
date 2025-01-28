<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Login failed'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if (!$username || !$password) {
        $response['message'] = 'Invalid username or password format';
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            if (password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['role'] = 'admin';
                
                $response = [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'redirect' => 'AdminDashboard.php'
                ];
            } else {
                $response['message'] = 'Invalid password';
            }
        } else {
            $response['message'] = 'Username not found';
        }
        
        $stmt->close();
    } else {
        $response['message'] = 'Database error occurred';
    }
}

echo json_encode($response);
$conn->close();
?>
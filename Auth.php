<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connection.php';

// Function to authorize user roles
function authorize($role) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: Login.php');
        exit();
    }
}

// Handle login POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'Login failed'];

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $response['message'] = 'Invalid email or password format';
        echo json_encode($response);
        exit();
    }

    // Prepare SQL to prevent injection
    $stmt = $conn->prepare("SELECT id, full_name, password, 'patient' as role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            $response = [
                'status' => 'success',
                'message' => 'Login successful',
                'redirect' => 'Patient.php?user_id=' . $user['id']
            ];
        } else {
            $response['message'] = 'Invalid password';
        }
    } else {
        $response['message'] = 'Email not found';
    }

    $stmt->close();
    echo json_encode($response);
    exit();
}

// If not a POST request and no session, redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

// $conn->close();
?>
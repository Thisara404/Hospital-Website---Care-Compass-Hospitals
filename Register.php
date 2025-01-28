<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Registration failed'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (!$full_name || !$email || !$password || $password !== $confirm_password) {
        $response['message'] = 'Invalid input or passwords do not match';
        echo json_encode($response);
        exit;
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $response['message'] = 'Email already registered';
        echo json_encode($response);
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $phone, $hashed_password);

    if ($stmt->execute()) {
        $response = [
            'status' => 'success', 
            'message' => 'Registration successful'
        ];
    }

    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>
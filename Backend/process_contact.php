<?php
require_once 'backend/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new DatabaseConnection();
    $conn = $db->conn;

    // Sanitize and validate inputs
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $message = $conn->real_escape_string($_POST['message']);

    // Optional: User ID if logged in (set this based on your authentication system)
    $user_id = null; 

    $sql = "INSERT INTO Contact_Submissions (user_id, name, email, phone, message) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $name, $email, $phone, $message);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message submitted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit message']);
    }

    $stmt->close();
    $db->closeConnection();
    exit();
}
?>
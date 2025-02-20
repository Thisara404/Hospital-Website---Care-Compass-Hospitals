<?php
// get_doctor.php
require_once 'db_connection.php';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No ID provided']);
    exit();
}

try {
    $db = new DatabaseConnection();
    $conn = $db->conn;
    
    $id = $conn->real_escape_string($_GET['id']);
    $query = "SELECT id, staff_id, full_name, email, department FROM staff WHERE id = ? AND position = 'Doctor'";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Doctor not found']);
    }
    
    $stmt->close();
    $db->closeConnection();
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
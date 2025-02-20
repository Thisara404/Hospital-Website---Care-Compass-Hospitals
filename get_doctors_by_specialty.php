<?php
// filepath: get_doctors_by_specialty.php
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    $db = new DatabaseConnection();
    $conn = $db->conn;
    
    // Get lab-related doctors
    $query = "SELECT id, staff_id, full_name FROM staff 
             WHERE position = 'Doctor' 
             AND department IN ('Pathology', 'Radiology', 'Clinical Laboratory', 'Hematology', 'Medical Imaging') 
             ORDER BY full_name";
    
    $result = $stmt = $conn->query($query);
    
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'doctors' => $doctors]);
    
    $db->closeConnection();
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
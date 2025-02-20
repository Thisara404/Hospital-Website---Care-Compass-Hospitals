<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'backend/db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['temp_appointment'])) {
    header('Location: ChannelingForm.php');
    exit();
}

$db = new DatabaseConnection();
$conn = $db->conn;

$appointment = $_SESSION['temp_appointment'];
$user_id = $_SESSION['user_id'];

$insert_query = "INSERT INTO appointments 
                 (user_id, doctor_id, specialty, appointment_date, time_slot, 
                  patient_name, contact_number, email, additional_notes, appointment_type) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$insert_stmt = $conn->prepare($insert_query);
$insert_stmt->bind_param("iisssssss", 
    $user_id, 
    $appointment['doctor_id'],
    $appointment['specialty'],
    $appointment['date'],
    $appointment['time_slot'],
    $appointment['patient_name'],
    $appointment['contact'],
    $appointment['email'],
    $appointment['notes'],
    $appointment['type']
);

if ($insert_stmt->execute()) {
    unset($_SESSION['temp_appointment']);
    $_SESSION['appointment_success'] = true;
    header('Location: Patient.php');
} else {
    $_SESSION['appointment_error'] = 'Failed to book appointment';
    header('Location: ChannelingForm.php');
}
exit();
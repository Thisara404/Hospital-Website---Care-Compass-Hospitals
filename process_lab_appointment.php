<?php
// filepath: process_lab_appointment.php
session_start();
require_once 'backend/db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['temp_lab_appointment'])) {
    header('Location: LaboratoryForm.php');
    exit();
}

$db = new DatabaseConnection();
$conn = $db->conn;

try {
    $conn->begin_transaction();

    $appointment = $_SESSION['temp_lab_appointment'];
    $user_id = $_SESSION['user_id'];

    // Insert appointment
    $insert_appointment = "INSERT INTO appointments 
                         (user_id, patient_name, appointment_date, time_slot,
                          contact_number, email, additional_notes, appointment_type) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 'laboratory')";

    $stmt1 = $conn->prepare($insert_appointment);
    $stmt1->bind_param("issssss",
        $user_id,
        $appointment['patient_name'],
        $appointment['test_date'],
        $appointment['time_slot'],
        $appointment['contact'],
        $appointment['email'],
        $appointment['notes']
    );
    $stmt1->execute();
    $appointment_id = $conn->insert_id;

    // Insert laboratory test details
    $insert_lab = "INSERT INTO laboratory_tests 
                  (appointment_id, test_category, specific_test, doctor_referral, fasting_required)
                  VALUES (?, ?, ?, ?, ?)";

    $stmt2 = $conn->prepare($insert_lab);
    $stmt2->bind_param("isssi",
        $appointment_id,
        $appointment['test_category'],
        $appointment['specific_test'],
        $appointment['doctor_referral'],
        $appointment['fasting']
    );

    if ($stmt2->execute()) {
        $conn->commit();
        unset($_SESSION['temp_lab_appointment']);
        $_SESSION['appointment_success'] = true;
        header('Location: Patient.php');
    } else {
        throw new Exception("Failed to insert laboratory test details");
    }

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['appointment_error'] = 'Failed to book laboratory test: ' . $e->getMessage();
    header('Location: LaboratoryForm.php');
}

$conn->close();
exit();
?>
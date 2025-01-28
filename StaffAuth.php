<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Login failed'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = filter_input(INPUT_POST, 'staff_id', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if (!$staff_id || !$password) {
        $response['message'] = 'Invalid staff ID or password format';
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, staff_id, full_name, password FROM staff WHERE staff_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $staff = $result->fetch_assoc();
            
            if (password_verify($password, $staff['password'])) {
                $_SESSION['user_id'] = $staff['id'];
                $_SESSION['staff_id'] = $staff['staff_id'];
                $_SESSION['full_name'] = $staff['full_name'];
                $_SESSION['role'] = 'staff';
                
                $response = [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'redirect' => 'StaffDashboard.php'
                ];
            } else {
                $response['message'] = 'Invalid password';
            }
        } else {
            $response['message'] = 'Staff ID not found';
        }
        
        $stmt->close();
    } else {
        $response['message'] = 'Database error occurred';
    }
}

echo json_encode($response);
$conn->close();
?>
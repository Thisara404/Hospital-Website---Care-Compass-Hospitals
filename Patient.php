<?php
session_start();
require_once 'db_connection.php';
require_once 'Auth.php';
authorize('patient');

// Get user data from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

try {
    // Create database connection
    $db = new DatabaseConnection();
    $conn = $db->conn;

    // Fetch patient's medical records
    $records_query = "SELECT * FROM medical_records WHERE patient_id = ? ORDER BY date DESC LIMIT 5";
    $stmt_records = $conn->prepare($records_query);
    if ($stmt_records === false) {
        throw new Exception("Failed to prepare medical records query: " . $conn->error);
    }
    $stmt_records->bind_param("i", $user_id);
    $stmt_records->execute();
    $medical_records = $stmt_records->get_result();

    // Success message display
    if (isset($_SESSION['appointment_success'])) {
        echo '<div class="alert alert-success">Appointment booked successfully!</div>';
        unset($_SESSION['appointment_success']);
    }

    // Fetch all appointments for the user
    $appointments_query = "
        SELECT 
            a.*,
            s.full_name as doctor_name,
            lt.test_category,
            lt.specific_test,
            lt.fasting_required
        FROM appointments a
        LEFT JOIN staff s ON a.doctor_id = s.id
        LEFT JOIN laboratory_tests lt ON a.id = lt.appointment_id
        WHERE a.user_id = ?
        ORDER BY a.appointment_date DESC, a.time_slot ASC";

    $stmt_appointments = $conn->prepare($appointments_query);
    if ($stmt_appointments === false) {
        throw new Exception("Failed to prepare appointments query: " . $conn->error);
    }
    $stmt_appointments->bind_param("i", $user_id);
    $stmt_appointments->execute();
    $appointments = $stmt_appointments->get_result();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Care Compass Hospitals</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <nav class="navbar">
                <div class="logo">
                    <img src="assets/logo.png" alt="Care Compass Logo">
                </div>
                <ul class="nav-links">
                    <li><a href="#medical-records">Medical Records</a></li>
                    <li><a href="ChannelingForm.php">Appointments</a></li>
                    <li><a href="#payments">Payments</a></li>
                    <li><a href="#queries">Queries</a></li>
                    <li><a href="#feedback">Feedback</a></li>
                    <li><a href="Logout.php" class="btn-primary">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="welcome-message">
                <h1>Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
                <p>Manage your healthcare with Care Compass Hospitals.</p>
            </section>

            <section id="medical-records" class="medical-records">
                <h2>My Medical Records</h2>
                <div class="records-container">
                    <?php if ($medical_records->num_rows > 0): ?>
                        <?php while($record = $medical_records->fetch_assoc()): ?>
                            <div class="record-card">
                                <h3><?php echo htmlspecialchars($record['record_type']); ?></h3>
                                <p>Date: <?php echo htmlspecialchars($record['date']); ?></p>
                                <p><?php echo htmlspecialchars($record['description']); ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No medical records found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="appointments" class="appointments">
                <h2>My Appointments</h2>
                <div class="appointments-container">
                    <?php if ($appointments->num_rows > 0): ?>
                        <?php while($appointment = $appointments->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <div class="appointment-type">
                                    <?php echo htmlspecialchars(ucfirst($appointment['appointment_type'])); ?> Appointment
                                </div>
                                <div class="appointment-details">
                                    <?php if ($appointment['appointment_type'] === 'channeling'): ?>
                                        <h3>Doctor: Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h3>
                                        <p>Specialty: <?php echo htmlspecialchars($appointment['specialty']); ?></p>
                                    <?php else: ?>
                                        <h3>Laboratory Test</h3>
                                        <p>Test: <?php echo htmlspecialchars($appointment['test_category']); ?></p>
                                        <?php if ($appointment['specific_test']): ?>
                                            <p>Specific Test: <?php echo htmlspecialchars($appointment['specific_test']); ?></p>
                                        <?php endif; ?>
                                        <?php if ($appointment['fasting_required']): ?>
                                            <p class="fasting-notice">Fasting Required</p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <p>Date: <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                                    <p>Time: <?php echo htmlspecialchars($appointment['time_slot']); ?></p>
                                    <p>Status: <span class="status-<?php echo htmlspecialchars($appointment['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($appointment['status'])); ?>
                                    </span></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No appointments found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Other sections remain the same -->
        </main>

        <footer>
            <p>&copy; 2025 Care Compass Hospitals. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
<?php $conn->close(); ?>
<?php
require_once 'db_connection.php';  // Include the database connection first
require_once 'Auth.php';
authorize('patient');

// Get user data from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Fetch patient's medical records
$records_query = $conn->prepare("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY date DESC LIMIT 5");
$records_query->bind_param("i", $user_id);
$records_query->execute();
$medical_records = $records_query->get_result();

// Fetch upcoming appointments
$appointments_query = $conn->prepare("SELECT * FROM appointments WHERE patient_id = ? AND appointment_date >= CURDATE() ORDER BY appointment_date ASC");
$appointments_query->bind_param("i", $user_id);
$appointments_query->execute();
$appointments = $appointments_query->get_result();
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
                    <li><a href="#appointments">Appointments</a></li>
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
                <h2>Upcoming Appointments</h2>
                <div class="appointments-container">
                    <?php if ($appointments->num_rows > 0): ?>
                        <?php while($appointment = $appointments->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <h3>Appointment with Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h3>
                                <p>Date: <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                                <p>Time: <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No upcoming appointments.</p>
                    <?php endif; ?>
                </div>
                <a href="schedule_appointment.php" class="btn-secondary">Schedule New Appointment</a>
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
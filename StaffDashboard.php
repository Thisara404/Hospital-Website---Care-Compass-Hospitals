<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is staff
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header('Location: Login.php');
    exit();
}

try {
    // Create database connection
    $db = new DatabaseConnection();
    $conn = $db->conn;

    $staff_name = $_SESSION['full_name'];
    $staff_id = $_SESSION['staff_id'];

    // Fetch today's appointments with error handling
    $query = "SELECT 
                a.*,
                p.full_name as patient_name,
                lt.test_category,
                lt.specific_test,
                lt.fasting_required 
              FROM appointments a
              LEFT JOIN users p ON a.user_id = p.id
              LEFT JOIN laboratory_tests lt ON a.id = lt.appointment_id
              WHERE a.staff_id = ? 
              AND a.appointment_date = CURDATE()
              ORDER BY a.time_slot ASC";
              
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        throw new Exception("Failed to prepare query: " . $conn->error);
    }

    $stmt->bind_param("s", $staff_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }

    $today_appointments = $stmt->get_result();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Care Compass Hospitals</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .appointment-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .patient-search {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <nav class="navbar">
                <div class="logo">Care Compass Staff</div>
                <div class="user-info">
                    Welcome, <?php echo htmlspecialchars($staff_name); ?>
                    <a href="Logout.php" class="btn-logout">Logout</a>
                </div>
            </nav>
        </header>

        <main>
            <section class="patient-search">
                <h2>Patient Search</h2>
                <div class="search-controls">
                    <input type="text" id="patient-search" placeholder="Enter patient name or ID...">
                    <button onclick="searchPatient()" class="btn-search">Search</button>
                </div>
                <div id="patient-results"></div>
            </section>

            <section class="today-appointments">
                <h2>Today's Appointments</h2>
                <div class="appointments-grid">
                    <?php while($appointment = $today_appointments->fetch_assoc()): ?>
                        <div class="appointment-card">
                            <h3>Patient: <?php echo htmlspecialchars($appointment['patient_name']); ?></h3>
                            <p>Time: <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                            <p>Type: <?php echo htmlspecialchars($appointment['appointment_type']); ?></p>
                            <button onclick="updateRecords(<?php echo $appointment['id']; ?>)" class="btn-primary">
                                Update Records
                            </button>
                            <button onclick="rescheduleAppointment(<?php echo $appointment['id']; ?>)" class="btn-secondary">
                                Reschedule
                            </button>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>

            <section class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <button onclick="location.href='schedule_appointment.php'" class="action-btn">
                        Schedule Appointment
                    </button>
                    <button onclick="location.href='update_records.php'" class="action-btn">
                        Update Records
                    </button>
                    <button onclick="location.href='test_results.php'" class="action-btn">
                        Update Test Results
                    </button>
                </div>
            </section>
        </main>
    </div>

    <script>
    function searchPatient() {
        const searchQuery = document.getElementById('patient-search').value;
        const resultsDiv = document.getElementById('patient-results');

        fetch(`search_patient.php?query=${encodeURIComponent(searchQuery)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    resultsDiv.innerHTML = createPatientTable(data.results);
                } else {
                    resultsDiv.innerHTML = `<p>No patients found</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultsDiv.innerHTML = `<p>An error occurred while searching</p>`;
            });
    }

    function createPatientTable(results) {
        // Create table HTML based on search results
        // Implementation details...
    }

    function updateRecords(appointmentId) {
        // Implementation for updating records
    }

    function rescheduleAppointment(appointmentId) {
        // Implementation for rescheduling appointments
    }
    </script>
</body>
</html>
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
        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: #2c3e50;
            color: white;
            padding: 2rem;
        }

        .sidebar .staff-info {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 2rem;
        }

        .sidebar .staff-info img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 1rem;
        }

        .main-content {
            padding: 2rem;
            background: #f8f9fa;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .appointment-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .appointment-card.lab-test {
            border-left: 4px solid #3498db;
        }

        .appointment-card.consultation {
            border-left: 4px solid #2ecc71;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .search-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .search-box {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="staff-info">
                <img src="assets/default-avatar.png" alt="Staff Photo">
                <h3><?php echo htmlspecialchars($staff_name); ?></h3>
                <p><?php echo htmlspecialchars($staff_id); ?></p>
            </div>
            <nav class="sidebar-nav">
                <a href="#dashboard" class="active">Dashboard</a>
                <a href="#appointments">Appointments</a>
                <a href="#patients">Patients</a>
                <a href="#lab-results">Lab Results</a>
                <a href="Logout.php">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Today's Appointments</h3>
                    <p class="stat-number"><?php echo $today_appointments->num_rows; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pending Lab Tests</h3>
                    <p class="stat-number">
                        <?php 
                        $pending_tests = $conn->query("SELECT COUNT(*) as count FROM laboratory_tests 
                            WHERE status='pending'")->fetch_assoc()['count'];
                        echo $pending_tests;
                        ?>
                    </p>
                </div>
                <div class="stat-card">
                    <h3>Total Patients</h3>
                    <p class="stat-number">
                        <?php 
                        $total_patients = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
                        echo $total_patients;
                        ?>
                    </p>
                </div>
            </div>

            <section class="search-section">
                <h2>Quick Search</h2>
                <div class="search-box">
                    <input type="text" id="patient-search" placeholder="Search patients by name, ID or phone number...">
                    <button onclick="searchPatient()" class="btn-primary">Search</button>
                </div>
                <div id="patient-results"></div>
            </section>

            <section class="appointments-section">
                <h2>Today's Appointments</h2>
                <div class="appointments-grid">
                    <?php while($appointment = $today_appointments->fetch_assoc()): ?>
                        <div class="appointment-card <?php echo $appointment['appointment_type']; ?>">
                            <div class="appointment-header">
                                <h3><?php echo htmlspecialchars($appointment['patient_name']); ?></h3>
                                <span class="time"><?php echo htmlspecialchars($appointment['time_slot']); ?></span>
                            </div>
                            
                            <?php if($appointment['appointment_type'] == 'laboratory'): ?>
                                <p><strong>Test:</strong> <?php echo htmlspecialchars($appointment['test_category']); ?></p>
                                <?php if($appointment['fasting_required']): ?>
                                    <p class="fasting-notice">⚠️ Fasting Required</p>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="action-buttons">
                                <button onclick="updateRecords(<?php echo $appointment['id']; ?>)" 
                                        class="btn-primary">Update Records</button>
                                <button onclick="rescheduleAppointment(<?php echo $appointment['id']; ?>)" 
                                        class="btn-secondary">Reschedule</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
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
        if (!results || results.length === 0) {
            return '<p>No patients found</p>';
        }

        let table = `
            <table class="patient-table">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

        results.forEach(patient => {
            table += `
                <tr>
                    <td>${patient.id}</td>
                    <td>${patient.full_name}</td>
                    <td>${patient.email}</td>
                    <td>${patient.phone || 'N/A'}</td>
                    <td>
                        <button onclick="viewPatientHistory(${patient.id})" class="btn-secondary">View History</button>
                        <button onclick="scheduleAppointment(${patient.id})" class="btn-primary">Schedule</button>
                    </td>
                </tr>
            `;
        });

        table += '</tbody></table>';
        return table;
    }

    function updateRecords(appointmentId) {
        // Create a modal for updating records
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>Update Patient Records</h2>
                <form id="update-records-form">
                    <div class="form-group">
                        <label for="notes">Medical Notes</label>
                        <textarea id="notes" name="notes" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="prescription">Prescription</label>
                        <textarea id="prescription" name="prescription"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="completed">Completed</option>
                            <option value="follow-up">Needs Follow-up</option>
                            <option value="referred">Referred</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save Records</button>
                        <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);

        // Handle form submission
        document.getElementById('update-records-form').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('appointment_id', appointmentId);

            fetch('update_appointment_records.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Records updated successfully');
                    location.reload();
                } else {
                    alert('Failed to update records: ' + data.message);
                }
                closeModal();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating records');
                closeModal();
            });
        };
    }

    function rescheduleAppointment(appointmentId) {
        // Create a modal for rescheduling
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>Reschedule Appointment</h2>
                <form id="reschedule-form">
                    <div class="form-group">
                        <label for="new-date">New Date</label>
                        <input type="date" id="new-date" name="new_date" required 
                               min="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="form-group">
                        <label for="new-time">New Time Slot</label>
                        <select id="new-time" name="new_time" required>
                            <option value="">Select Time Slot</option>
                            <option value="09:00">09:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="14:00">02:00 PM</option>
                            <option value="15:00">03:00 PM</option>
                            <option value="16:00">04:00 PM</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason for Rescheduling</label>
                        <textarea id="reason" name="reason" required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Confirm Reschedule</button>
                        <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);

        // Handle form submission
        document.getElementById('reschedule-form').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('appointment_id', appointmentId);

            fetch('reschedule_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Appointment rescheduled successfully');
                    location.reload();
                } else {
                    alert('Failed to reschedule appointment: ' + data.message);
                }
                closeModal();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while rescheduling');
                closeModal();
            });
        };
    }

    // Helper function to close modals
    function closeModal() {
        const modal = document.querySelector('.modal');
        if (modal) {
            modal.remove();
        }
    }
    </script>
</body>
</html>
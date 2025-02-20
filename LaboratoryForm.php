<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Services - Care Compass Hospitals</title>
    <link rel="stylesheet" href="form.css">
    <script src="script.js" defer></script>
</head>

<body>
    <!-- Header (same as other pages) -->
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="assets/logo.png" alt="Care Compass Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.html#services">Services</a></li>
                <li><a href="index.html#about">About Us</a></li>
                <li><a href="index.html#contact">Contact</a></li>
                <li><a href="login.php" id="Login-btn" class="btn-primary">Login</a></li>
            </ul>
        </nav>
    </header>

    <section class="laboratory-form-section">
        <div class="form-container">
            <h1>Laboratory Test Booking</h1>
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            require_once 'backend/db_connection.php';

            if (isset($_SESSION['appointment_error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['appointment_error']) . '</div>';
                unset($_SESSION['appointment_error']);
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Store form data in session
                $_SESSION['temp_lab_appointment'] = [
                    'test_category' => $_POST['test-category'],
                    'specific_test' => $_POST['specific-test'],
                    'test_date' => $_POST['test-date'],
                    'time_slot' => $_POST['time-slot'],
                    'patient_name' => $_POST['patient-name'],
                    'contact' => $_POST['patient-contact'],
                    'email' => $_POST['patient-email'],
                    'doctor_referral' => $_POST['doctor-referral'] ?? '',
                    'notes' => $_POST['additional-notes'] ?? '',
                    'fasting' => isset($_POST['fasting']) ? 1 : 0
                ];

                // Check if user is logged in
                if (!isset($_SESSION['user_id'])) {
                    header('Location: Login.php?redirect=lab_appointment');
                    exit();
                }

                // If user is logged in, process the appointment
                $db = new DatabaseConnection();
                $conn = $db->conn;

                try {
                    $conn->begin_transaction();
                    
                    // Debug logging
                    error_log("Processing lab appointment for user: " . $user_id);
                    error_log("Appointment data: " . print_r($appointment, true));

                    // Get data from session
                    $appointment = $_SESSION['temp_lab_appointment'];
                    $user_id = $_SESSION['user_id'];

                    // Insert appointment
                    $insert_appointment = "INSERT INTO appointments 
                                         (user_id, patient_name, appointment_date, time_slot,
                                          contact_number, email, additional_notes, appointment_type) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, 'laboratory')";

                    $stmt1 = $conn->prepare($insert_appointment);
                    $stmt1->bind_param("issssss",  // 7 parameters
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
                    $doctor_referral = (int)$appointment['doctor_referral']; // Convert to integer
                    $stmt2->bind_param("issii", // Note the 'ii' for integers
                        $appointment_id,
                        $appointment['test_category'],
                        $appointment['specific_test'],
                        $doctor_referral,
                        $appointment['fasting']
                    );

                    if ($stmt2->execute()) {
                        $conn->commit();
                        error_log("Lab appointment booked successfully. Appointment ID: " . $appointment_id);
                        unset($_SESSION['temp_lab_appointment']);
                        $_SESSION['appointment_success'] = true;
                        header('Location: Patient.php');
                        exit();
                    } else {
                        throw new Exception("Failed to insert laboratory test details");
                    }

                } catch (Exception $e) {
                    error_log("Lab appointment error: " . $e->getMessage());
                    $conn->rollback();
                    $_SESSION['appointment_error'] = 'Failed to book laboratory test: ' . $e->getMessage();
                    header('Location: LaboratoryForm.php');
                    exit();
                }

                $conn->close();
            }
            ?>
            <form id="laboratory-form" method="POST">
                <div class="form-group">
                    <label for="test-category">Select Test Category</label>
                    <select id="test-category" name="test-category" required>
                        <option value="">Choose a Test Category</option>
                        <optgroup label="Blood Tests">
                            <option value="cbc">Complete Blood Count (CBC)</option>
                            <option value="lipid-profile">Lipid Profile</option>
                            <option value="diabetes-screening">Diabetes Screening</option>
                            <option value="thyroid">Thyroid Function Tests</option>
                        </optgroup>
                        <optgroup label="Imaging Services">
                            <option value="xray">X-Ray</option>
                            <option value="mri">MRI Scan</option>
                            <option value="ct-scan">CT Scan</option>
                            <option value="ultrasound">Ultrasound</option>
                        </optgroup>
                        <optgroup label="Specialized Tests">
                            <option value="urine-analysis">Urine Analysis</option>
                            <option value="hormone-panel">Hormone Panel</option>
                            <option value="genetic-test">Genetic Testing</option>
                            <option value="cancer-screening">Cancer Screening</option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="specific-test">Specific Test</label>
                    <select id="specific-test" name="specific-test" required disabled>
                        <option value="">First Select a Test Category</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="test-date">Preferred Date</label>
                    <input type="date" id="test-date" name="test-date" required>
                </div>

                <div class="form-group">
                    <label for="time-slot">Preferred Time Slot</label>
                    <select id="time-slot" name="time-slot" required>
                        <option value="">Select a Time Slot</option>
                        <option value="morning-early">Early Morning (7:00 AM - 9:00 AM)</option>
                        <option value="morning-late">Late Morning (9:00 AM - 11:00 AM)</option>
                        <option value="afternoon-early">Early Afternoon (11:00 AM - 1:00 PM)</option>
                        <option value="afternoon-late">Late Afternoon (1:00 PM - 3:00 PM)</option>
                        <option value="evening">Evening (3:00 PM - 5:00 PM)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="patient-name">Patient Name</label>
                    <input type="text" id="patient-name" name="patient-name" required>
                </div>

                <div class="form-group">
                    <label for="patient-contact">Contact Number</label>
                    <input type="tel" id="patient-contact" name="patient-contact" required>
                </div>

                <div class="form-group">
                    <label for="patient-email">Email Address</label>
                    <input type="email" id="patient-email" name="patient-email" required>
                </div>

                <div class="form-group">
                    <label for="referring-doctor">Referring Doctor</label>
                    <select id="referring-doctor" name="doctor-referral" required>
                        <option value="">Select Doctor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="additional-notes">Additional Notes</label>
                    <textarea id="additional-notes" name="additional-notes" rows="4" placeholder="Any special instructions or medical history"></textarea>
                </div>

                <div class="form-group">
                    <input type="checkbox" id="fasting" name="fasting">
                    <label for="fasting">Fasting Required (for certain blood tests)</label>
                </div>

                <button type="submit" class="btn-primary">Book Laboratory Test</button>
            </form>
        </div>
    </section>

    <!-- Footer (same as other pages) -->
    <footer>
        <p>&copy; 2025 Care Compass Hospitals. All rights reserved.</p>
    </footer>

    <script>
        // Dynamic specific test selection based on test category
        const testCategorySelect = document.getElementById('test-category');
        const specificTestSelect = document.getElementById('specific-test');

        const testsByCategory = {
            'cbc': ['Basic CBC', 'Comprehensive CBC', 'Pediatric CBC'],
            'lipid-profile': ['Standard Lipid Panel', 'Advanced Lipid Panel'],
            'xray': ['Chest X-Ray', 'Bone X-Ray', 'Dental X-Ray'],
            'mri': ['Brain MRI', 'Spine MRI', 'Joint MRI'],
            // Add more tests for each category
        };

        testCategorySelect.addEventListener('change', function() {
            specificTestSelect.disabled = false;
            specificTestSelect.innerHTML = '<option value="">Select Specific Test</option>';

            const tests = testsByCategory[this.value] || [];
            tests.forEach(test => {
                const option = document.createElement('option');
                option.value = test.replace(/\s+/g, '-').toLowerCase();
                option.textContent = test;
                specificTestSelect.appendChild(option);
            });
        });

        // Add this after your existing testCategorySelect script
        fetch('get_doctors_by_specialty.php')
            .then(response => response.json())
            .then(data => {
                const doctorSelect = document.getElementById('referring-doctor');
                if (data.status === 'success') {
                    console.log('Doctors loaded:', data.doctors); // Debug log
                    doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
                    data.doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.id;
                        option.textContent = `Dr. ${doctor.full_name} (${doctor.department})`;
                        doctorSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading doctors:', error);
                doctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
            });
    </script>
</body>

</html>
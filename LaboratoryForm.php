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
            require_once 'backend/db_connection.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $db = new DatabaseConnection();
                $conn = $db->conn;

                // Sanitize inputs
                $test_category = $conn->real_escape_string($_POST['test-category']);
                $specific_test = $conn->real_escape_string($_POST['specific-test']);
                $test_date = $conn->real_escape_string($_POST['test-date']);
                $time_slot = $conn->real_escape_string($_POST['time-slot']);
                $patient_name = $conn->real_escape_string($_POST['patient-name']);
                $contact = $conn->real_escape_string($_POST['patient-contact']);
                $email = $conn->real_escape_string($_POST['patient-email']);
                $doctor_referral = $conn->real_escape_string($_POST['doctor-referral'] ?? '');
                $notes = $conn->real_escape_string($_POST['additional-notes'] ?? '');
                $fasting = isset($_POST['fasting']) ? 1 : 0;

                // Optional: User ID if logged in
                $user_id = null;

                $insert_query = "INSERT INTO Laboratory_Tests 
                                 (user_id, test_category, specific_test, test_date, time_slot, 
                                  patient_name, contact_number, email, doctor_referral, 
                                  additional_notes, fasting_required) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param(
                    "isssssssssi",
                    $user_id,
                    $test_category,
                    $specific_test,
                    $test_date,
                    $time_slot,
                    $patient_name,
                    $contact,
                    $email,
                    $doctor_referral,
                    $notes,
                    $fasting
                );

                if ($insert_stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Laboratory test booked successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to book laboratory test']);
                }

                $insert_stmt->close();
                $db->closeConnection();
                exit();
            }
            ?>
            <form id="laboratory-form" action="process_laboratory.php" method="POST">
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
                    <label for="doctor-referral">Doctor's Referral (Optional)</label>
                    <input type="text" id="doctor-referral" name="doctor-referral" placeholder="Referring Doctor's Name">
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
    </script>
</body>

</html>
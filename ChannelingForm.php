<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'backend/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new DatabaseConnection();
    $conn = $db->conn;

    // Sanitize inputs
    $specialty = $conn->real_escape_string($_POST['specialty']);
    $doctor_id = $conn->real_escape_string($_POST['doctor']);
    $date = $conn->real_escape_string($_POST['appointment-date']);
    $time_slot = $conn->real_escape_string($_POST['time-slot']);
    $patient_name = $conn->real_escape_string($_POST['patient-name']);
    $contact = $conn->real_escape_string($_POST['patient-contact']);
    $email = $conn->real_escape_string($_POST['patient-email']);
    $notes = $conn->real_escape_string($_POST['additional-notes'] ?? '');

    // Store appointment data in session
    $_SESSION['temp_appointment'] = [
        'specialty' => $specialty,
        'doctor_id' => $doctor_id,
        'date' => $date,
        'time_slot' => $time_slot,
        'patient_name' => $patient_name,
        'contact' => $contact,
        'email' => $email,
        'notes' => $notes,
        'type' => 'channeling'
    ];

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: user_check.php');
        exit();
    }

    // If user is logged in, proceed with booking
    $user_id = $_SESSION['user_id'];
    $insert_query = "INSERT INTO appointments 
                     (user_id, doctor_id, specialty, appointment_date, time_slot, 
                      patient_name, contact_number, email, additional_notes, appointment_type) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'channeling')";

    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("iisssssss", 
        $user_id, $doctor_id, $specialty, $date, $time_slot, 
        $patient_name, $contact, $email, $notes
    );

    if ($insert_stmt->execute()) {
        $_SESSION['appointment_success'] = true;
        header('Location: Patient.php');
        exit();
    } else {
        $_SESSION['appointment_error'] = 'Failed to book appointment';
        header('Location: ChannelingForm.php');
        exit();
    }

    $insert_stmt->close();
    $db->closeConnection();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Channeling - Care Compass Hospitals</title>
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

    <section class="channeling-form-section">
        <div class="form-container">
            <h1>Doctor Channeling Appointment</h1>
            <form id="channeling-form" action="ChannelingForm.php" method="POST">
                <div class="form-group">
                    <label for="specialty">Select Specialty</label>
                    <select id="specialty" name="specialty" required>
                        <option value="">Choose a Specialty</option>
                        <optgroup label="General Medicine">
                            <option value="general-checkup">Routine Check-ups</option>
                            <option value="general-diagnosis">Diagnosis of Common Illnesses</option>
                            <option value="wellness-counseling">Health and Wellness Counseling</option>
                        </optgroup>
                        <optgroup label="Specialist Consultations">
                            <option value="endocrinology">Endocrinology (Diabetes & Hormonal Disorders)</option>
                            <option value="cardiology">Cardiology (Heart Health)</option>
                            <option value="gastroenterology">Gastroenterology (Digestive System)</option>
                            <option value="neurology">Neurology (Nervous System)</option>
                            <option value="pulmonology">Pulmonology (Respiratory Health)</option>
                        </optgroup>
                        <optgroup label="Pediatrics">
                            <option value="child-checkup">Child Health Check-ups</option>
                            <option value="vaccinations">Vaccinations</option>
                            <option value="child-development">Growth and Development Monitoring</option>
                        </optgroup>
                        <optgroup label="Women's Health">
                            <option value="prenatal-care">Prenatal Care</option>
                            <option value="menstrual-health">Menstrual Health Consultations</option>
                            <option value="fertility">Fertility Counseling</option>
                        </optgroup>
                        <optgroup label="Other Specialties">
                            <option value="orthopedics">Orthopedics (Bone and Joint Health)</option>
                            <option value="dermatology">Dermatology (Skin Conditions)</option>
                            <option value="ent">ENT (Ear, Nose, and Throat)</option>
                            <option value="mental-health">Psychiatry and Mental Health</option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="doctor">Select Doctor</label>
                    <select id="doctor" name="doctor" required disabled>
                        <option value="">First Select a Specialty</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="appointment-date">Preferred Date</label>
                    <input type="date" id="appointment-date" name="appointment-date" required>
                </div>

                <div class="form-group">
                    <label for="time-slot">Preferred Time Slot</label>
                    <select id="time-slot" name="time-slot" required>
                        <option value="">Select a Time Slot</option>
                        <option value="morning-early">Early Morning (8:00 AM - 10:00 AM)</option>
                        <option value="morning-late">Late Morning (10:00 AM - 12:00 PM)</option>
                        <option value="afternoon-early">Early Afternoon (12:00 PM - 2:00 PM)</option>
                        <option value="afternoon-late">Late Afternoon (2:00 PM - 4:00 PM)</option>
                        <option value="evening">Evening (4:00 PM - 6:00 PM)</option>
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
                    <label for="additional-notes">Additional Notes (Optional)</label>
                    <textarea id="additional-notes" name="additional-notes" rows="4"></textarea>
                </div>

                <button type="submit" class="btn-primary">Book Appointment</button>
            </form>
        </div>
    </section>

    <!-- Footer (same as other pages) -->
    <footer>
        <p>&copy; 2025 Care Compass Hospitals. All rights reserved.</p>
    </footer>

    <script>
        const specialtySelect = document.getElementById('specialty');
        const doctorSelect = document.getElementById('doctor');

        specialtySelect.addEventListener('change', function() {
            const specialty = this.value;
            doctorSelect.disabled = true;
            doctorSelect.innerHTML = '<option value="">Loading doctors...</option>';
            
            fetch(`get_doctors_by_specialty.php?specialty=${encodeURIComponent(specialty)}`)
                .then(response => response.json())
                .then(data => {
                    doctorSelect.innerHTML = '<option value="">Select a Doctor</option>';
                    if (data.status === 'success') {
                        data.doctors.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = `Dr. ${doctor.full_name}`;
                            doctorSelect.appendChild(option);
                        });
                        doctorSelect.disabled = false;
                    } else {
                        doctorSelect.innerHTML = '<option value="">No doctors available</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    doctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
                });
        });
    </script>
</body>
</html>
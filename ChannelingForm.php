<?php
require_once 'backend/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new DatabaseConnection();
    $conn = $db->conn;

    // Sanitize inputs
    $specialty = $conn->real_escape_string($_POST['specialty']);
    $doctor_name = $conn->real_escape_string($_POST['doctor']);
    $date = $conn->real_escape_string($_POST['appointment-date']);
    $time_slot = $conn->real_escape_string($_POST['time-slot']);
    $patient_name = $conn->real_escape_string($_POST['patient-name']);
    $contact = $conn->real_escape_string($_POST['patient-contact']);
    $email = $conn->real_escape_string($_POST['patient-email']);
    $notes = $conn->real_escape_string($_POST['additional-notes'] ?? '');

    // Get doctor_id
    $doctor_query = "SELECT doctor_id FROM Doctors WHERE name = ?";
    $doctor_stmt = $conn->prepare($doctor_query);
    $doctor_stmt->bind_param("s", $doctor_name);
    $doctor_stmt->execute();
    $doctor_result = $doctor_stmt->get_result();
    
    if ($doctor_result->num_rows > 0) {
        $doctor_row = $doctor_result->fetch_assoc();
        $doctor_id = $doctor_row['doctor_id'];

        // Optional: User ID if logged in
        $user_id = null;

        $insert_query = "INSERT INTO Appointments 
                         (user_id, doctor_id, specialty, appointment_date, time_slot, 
                          patient_name, contact_number, email, additional_notes) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iisssssss", 
            $user_id, $doctor_id, $specialty, $date, $time_slot, 
            $patient_name, $contact, $email, $notes
        );

        if ($insert_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Appointment booked successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to book appointment']);
        }

        $insert_stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Doctor not found']);
    }

    $doctor_stmt->close();
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

    <!-- <script>
        // Dynamic doctor selection based on specialty
        const specialtySelect = document.getElementById('specialty');
        const doctorSelect = document.getElementById('doctor');

        const doctorsBySpecialty = {
            'general-checkup': ['Dr. Sarah Johnson', 'Dr. Michael Lee'],
            'cardiology': ['Dr. Robert Chen', 'Dr. Emily Rodriguez'],
            'endocrinology': ['Dr. Amanda Wong', 'Dr. David Kim'],
            // Add more doctors for each specialty
        };

        specialtySelect.addEventListener('change', function() {
            doctorSelect.disabled = false;
            doctorSelect.innerHTML = '<option value="">Select a Doctor</option>';
            
            const doctors = doctorsBySpecialty[this.value] || [];
            doctors.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.replace(/\s+/g, '-').toLowerCase();
                option.textContent = doctor;
                doctorSelect.appendChild(option);
            });
        });
    </script> -->
</body>
</html>
<?php
require_once 'Auth.php';
authorize('staff');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Care Compass Hospitals</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <header>
            <nav class="navbar">
                <div class="logo">
                    <img src="../assets/logo.png" alt="Care Compass Logo">
                </div>
                <ul class="nav-links">
                    <li><a href="#">Patient Records</a></li>
                    <li><a href="#">Appointment Management</a></li>
                    <li><a href="#">Query Responses</a></li>
                    <li><a href="#">Profile Settings</a></li>
                    <li><a href="Logout.php" class="btn-primary">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="welcome-message">
                <h1>Welcome, [Staff Name]</h1>
                <p>Manage hospital operations from your dashboard.</p>
            </section>

            <section class="patient-records">
                <h2>Patient Records</h2>
                <div class="records-container">
                    <!-- Dynamically display list of patients and their records -->
                </div>
            </section>

            <section class="appointment-management">
                <h2>Appointment Management</h2>
                <div class="appointments-container">
                    <!-- Dynamically display upcoming appointments and management tools -->
                </div>
            </section>

            <section class="query-responses">
                <h2>Patient Queries</h2>
                <div class="queries-container">
                    <!-- Dynamically display list of patient queries and response options -->
                </div>
            </section>

            <section class="profile-settings">
                <h2>My Profile</h2>
                <div class="profile-container">
                    <!-- Dynamically display and allow editing of staff member's profile -->
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 Care Compass Hospitals. All rights reserved.</p>
        </footer>
    </div>

    <script>
        // Add dynamic functionality to the dashboard
    </script>
</body>

</html>
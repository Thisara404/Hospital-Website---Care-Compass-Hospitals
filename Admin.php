<?php
require_once 'Auth.php';
authorize('admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Care Compass Hospitals</title>
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
                    <li><a href="#">User Management</a></li>
                    <li><a href="#">Doctor Profiles</a></li>
                    <li><a href="#">Query Responses</a></li>
                    <li><a href="#">Billing and Payments</a></li>
                    <li><a href="#">Reports</a></li>
                    <li><a href="Logout.php" class="btn-primary">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="welcome-message">
                <h1>Welcome, [Admin Name]</h1>
                <p>Manage all aspects of the hospital from your dashboard.</p>
            </section>

            <section class="user-management">
                <h2>User Management</h2>
                <div class="users-container">
                    <!-- Dynamically display list of users (patients and staff) and management tools -->
                </div>
            </section>

            <section class="doctor-profiles">
                <h2>Doctor Profiles</h2>
                <div class="doctors-container">
                    <!-- Dynamically display list of doctors and profile management tools -->
                </div>
                <a href="#" class="btn-secondary">Add New Doctor</a>
            </section>

            <section class="query-responses">
                <h2>Patient Queries</h2>
                <div class="queries-container">
                    <!-- Dynamically display list of patient queries and response management tools -->
                </div>
            </section>

            <section class="billing-payments">
                <h2>Billing and Payments</h2>
                <div class="billing-container">
                    <!-- Dynamically display billing and payment management tools -->
                </div>
            </section>

            <section class="reports">
                <h2>Reports</h2>
                <div class="reports-container">
                    <!-- Dynamically display various reports (e.g., patient satisfaction, service utilization) -->
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
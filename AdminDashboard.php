<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

$admin_name = $_SESSION['username'];

// Fetch quick statistics
$total_doctors = $conn->query("SELECT COUNT(*) as count FROM staff WHERE position = 'Doctor'")->fetch_assoc()['count'];
$total_patients = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_staff = $conn->query("SELECT COUNT(*) as count FROM staff")->fetch_assoc()['count'];
$pending_queries = $conn->query("SELECT COUNT(*) as count FROM queries WHERE status = 'pending'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Care Compass Hospitals</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .action-btn {
            padding: 15px;
            border: none;
            border-radius: 5px;
            background: #2196F3;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }

        .action-btn:hover {
            background: #1976D2;
        }

        .search-section {
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
                <div class="logo">
                    <img src="assets/logo.png" alt="Care Compass Logo">
                </div>
                <div class="logo">Care Compass Admin</div>
                <div class="user-info">
                    Welcome, <?php echo htmlspecialchars($admin_name); ?>
                    <a href="Logout.php" class="btn-logout">Logout</a>
                </div>
            </nav>
        </header>

        <main>
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Doctors</h3>
                    <p><?php echo $total_doctors; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Patients</h3>
                    <p><?php echo $total_patients; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Staff</h3>
                    <p><?php echo $total_staff; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Pending Queries</h3>
                    <p><?php echo $pending_queries; ?></p>
                </div>
            </div>

            <div class="quick-actions">
                <button onclick="location.href='manage_doctors.php'" class="action-btn">Manage Doctors</button>
                <button onclick="location.href='manage_staff.php'" class="action-btn">Manage Staff</button>
                <button onclick="location.href='manage_patients.php'" class="action-btn">Manage Patients</button>
                <button onclick="location.href='manage_appointments.php'" class="action-btn">Manage Appointments</button>
                <button onclick="location.href='manage_queries.php'" class="action-btn">Handle Queries</button>
                <button onclick="location.href='billing_overview.php'" class="action-btn">Billing Overview</button>
            </div>

            <div class="search-section">
                <h2>Quick Search</h2>
                <div class="search-controls">
                    <select id="search-type">
                        <option value="doctor">Search Doctors</option>
                        <option value="patient">Search Patients</option>
                        <option value="staff">Search Staff</option>
                    </select>
                    <input type="text" id="search-input" placeholder="Enter name, ID, or email...">
                    <button onclick="performSearch()" class="btn-search">Search</button>
                </div>
                <div id="search-results"></div>
            </div>
        </main>
    </div>

    <script>
        function performSearch() {
            const searchType = document.getElementById('search-type').value;
            const searchQuery = document.getElementById('search-input').value;
            const resultsDiv = document.getElementById('search-results');

            fetch(`search.php?type=${searchType}&query=${encodeURIComponent(searchQuery)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        resultsDiv.innerHTML = createResultsTable(data.results);
                    } else {
                        resultsDiv.innerHTML = `<p>No results found</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsDiv.innerHTML = `<p>An error occurred while searching</p>`;
                });
        }

        function createResultsTable(results) {
            // Create table HTML based on search results
            // Implementation details...
        }
    </script>
</body>

</html>
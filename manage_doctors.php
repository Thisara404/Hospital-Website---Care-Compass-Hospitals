<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

// Create database connection
try {
    $db = new DatabaseConnection();
    $conn = $db->conn;
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle doctor operations (add/edit/delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $staff_id = 'DOC' . rand(1000, 9999);
                $full_name = $conn->real_escape_string($_POST['full_name']);
                $email = $conn->real_escape_string($_POST['email']);
                $department = $conn->real_escape_string($_POST['department']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                $query = "INSERT INTO staff (staff_id, full_name, email, password, department, position) 
                         VALUES (?, ?, ?, ?, ?, 'Doctor')";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $staff_id, $full_name, $email, $password, $department);
                $stmt->execute();
                break;

            case 'edit':
                $id = $conn->real_escape_string($_POST['id']);
                $full_name = $conn->real_escape_string($_POST['full_name']);
                $email = $conn->real_escape_string($_POST['email']);
                $department = $conn->real_escape_string($_POST['department']);
                
                $query = "UPDATE staff SET full_name=?, email=?, department=? 
                         WHERE id=? AND position='Doctor'";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssi", $full_name, $email, $department, $id);
                $stmt->execute();
                break;

            case 'delete':
                $id = $conn->real_escape_string($_POST['id']);
                $query = "DELETE FROM staff WHERE id=? AND position='Doctor'";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
}

// Pagination setup
$items_per_page = 12;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Fetch total number of doctors
$total_doctors = $conn->query("SELECT COUNT(*) as count FROM staff WHERE position='Doctor'")->fetch_assoc()['count'];
$total_pages = ceil($total_doctors / $items_per_page);

// Fetch doctors with pagination
$doctors = $conn->query("SELECT * FROM staff WHERE position='Doctor' 
                        ORDER BY full_name LIMIT $offset, $items_per_page");

$doctors = $conn->query("SELECT * FROM staff WHERE position='Doctor' ORDER BY full_name");
$total_doctors = $doctors->num_rows;
echo "<!-- Total doctors found: " . $total_doctors . " -->"; // Debugging line
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - Care Compass Hospitals</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        :root {
            --dark-gray: #5f7174;
            --mid-gray: #6a888c;
            --blue: #00a6c0;
            --light-blue: #32d9cb;
        }
        /* Main container styles */
        .doctors-container {
            padding: 2rem;
            width: 100%;
            max-width: 1200px; /* Increased from 1200px */
            margin: 0 auto;
        }

        /* Header and button styles */
        .doctors-container h1 {
            color: var(--dark-gray);
            margin-bottom: 2rem;
        }

        .btn-add {
            background: var(--blue);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .btn-add:hover {
            background: var(--light-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Grid layout for doctor cards */
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
            width: 100%;
        }

        /* Doctor card styles */
        .doctor-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .doctor-card h3 {
            color: var(--dark-gray);
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .doctor-card p {
            color: var(--mid-gray);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .doctor-card strong {
            color: var(--dark-gray);
            font-weight: 600;
        }

        /* Action buttons container */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .action-buttons button {
            flex: 1;
            padding: 0.6rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-buttons button:first-child {
            background: var(--blue);
            color: white;
        }

        .action-buttons button:last-child {
            background: #dc3545;
            color: white;
        }

        .action-buttons button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            margin: 2rem auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .modal-content h2 {
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        /* Form group styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-gray);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(0, 166, 192, 0.1);
        }

        /* Form buttons */
        .form-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .form-buttons button {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--blue);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-primary:hover,
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Make the main element take full width */
        main {
            width: 100%;
            padding: 20px;
        }

        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: var(--dark-gray);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .pagination a.active {
            background-color: var(--blue);
            color: white;
            border-color: var(--blue);
        }

        .pagination a:hover {
            background-color: var(--light-blue);
            color: white;
        }

        /* Reset container width and add responsive padding */
        .dashboard-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Update main content area */
        main {
            flex: 1;
            padding: 2rem;
            background: #f8f9fa;
        }

        /* Update doctors container */
        .doctors-container {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Improve grid layout */
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
            padding-bottom: 2rem;
        }

        /* Enhanced doctor card styles */
        .doctor-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Card content layout */
        .doctor-info {
            flex: 1;
        }

        .doctor-card h3 {
            color: var(--dark-gray);
            margin-bottom: 1rem;
            font-size: 1.25rem;
            line-height: 1.4;
        }

        .doctor-card p {
            color: var(--mid-gray);
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Responsive adjustments */
        @media (max-width: 1400px) {
            .doctors-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .doctors-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .doctors-container {
                padding: 0 1rem;
            }
        }

        /* Pagination container if needed */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            padding-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <nav class="navbar">
                <div class="logo">
                    <img src="assets/logo.png" alt="Care Compass Logo">
                    <span>Care Compass Admin</span>
                </div>
                <div class="user-info">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <a href="Logout.php" class="btn-logout">Logout</a>
                </div>
            </nav>
        </header>

        <main>
            <div class="doctors-container">
                <h1>Manage Doctors</h1>
                <button class="btn-add" onclick="showAddModal()">Add New Doctor</button>

                <div class="doctors-grid">
                    <?php if ($doctors->num_rows > 0): ?>
                        <?php while($doctor = $doctors->fetch_assoc()): ?>
                            <div class="doctor-card">
                                <div class="doctor-info">
                                    <h3><?php echo htmlspecialchars($doctor['full_name']); ?></h3>
                                    <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($doctor['staff_id']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['email']); ?></p>
                                    <p><strong>Department:</strong> <?php echo htmlspecialchars($doctor['department']); ?></p>
                                </div>
                                <div class="action-buttons">
                                    <button onclick="editDoctor(<?php echo $doctor['id']; ?>)">Edit</button>
                                    <button onclick="deleteDoctor(<?php echo $doctor['id']; ?>)">Delete</button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No doctors found in the system.</p>
                    <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="<?php echo $i === $current_page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add/Edit Doctor Modal -->
    <div id="doctorModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Add New Doctor</h2>
            <form id="doctorForm" method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="doctorId">

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="Cardiology">Cardiology</option>
                        <option value="Neurology">Neurology</option>
                        <option value="Pediatrics">Pediatrics</option>
                        <option value="Orthopedics">Orthopedics</option>
                        <option value="General Medicine">General Medicine</option>
                    </select>
                </div>

                <div class="form-group password-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Doctor';
            document.getElementById('formAction').value = 'add';
            document.getElementById('doctorForm').reset();
            document.querySelector('.password-group').style.display = 'block';
            document.getElementById('doctorModal').style.display = 'block';
        }

        function editDoctor(id) {
            fetch(`get_doctor.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Doctor';
                    document.getElementById('formAction').value = 'edit';
                    document.getElementById('doctorId').value = id;
                    document.getElementById('full_name').value = data.full_name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('department').value = data.department;
                    document.querySelector('.password-group').style.display = 'none';
                    document.getElementById('doctorModal').style.display = 'block';
                });
        }

        function deleteDoctor(id) {
            if (confirm('Are you sure you want to delete this doctor?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('doctorModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('doctorModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
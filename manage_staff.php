<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $staff_id = generateStaffId();
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO staff (staff_id, full_name, email, password, department, position) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $staff_id, $_POST['full_name'], $_POST['email'], 
                                $hashed_password, $_POST['department'], $_POST['position']);
                $stmt->execute();
                break;

            case 'update':
                $stmt = $conn->prepare("UPDATE staff SET full_name=?, email=?, department=?, position=? 
                                      WHERE id=?");
                $stmt->bind_param("ssssi", $_POST['full_name'], $_POST['email'], 
                                $_POST['department'], $_POST['position'], $_POST['id']);
                $stmt->execute();
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM staff WHERE id=?");
                $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                break;
        }
    }
}

// Fetch all staff members
$staff = $conn->query("SELECT * FROM staff WHERE position != 'Doctor' ORDER BY created_at DESC");

function generateStaffId() {
    return 'STAFF' . date('Y') . rand(1000, 9999);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Care Compass</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .staff-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <nav class="navbar">
                <div class="logo">Care Compass Admin</div>
                <a href="AdminDashboard.php" class="btn">Back to Dashboard</a>
            </nav>
        </header>

        <main>
            <h2>Manage Staff</h2>
            
            <!-- Add Staff Form -->
            <div class="form-section">
                <h3>Add New Staff Member</h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" required>
                            <option value="Administration">Administration</option>
                            <option value="Nursing">Nursing</option>
                            <option value="Laboratory">Laboratory</option>
                            <option value="Pharmacy">Pharmacy</option>
                            <option value="Reception">Reception</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <select name="position" required>
                            <option value="Nurse">Nurse</option>
                            <option value="Lab Technician">Lab Technician</option>
                            <option value="Pharmacist">Pharmacist</option>
                            <option value="Receptionist">Receptionist</option>
                            <option value="Administrator">Administrator</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Add Staff Member</button>
                </form>
            </div>

            <!-- Staff List -->
            <div class="staff-grid">
                <?php while($member = $staff->fetch_assoc()): ?>
                    <div class="staff-card">
                        <h3><?php echo htmlspecialchars($member['full_name']); ?></h3>
                        <p>Staff ID: <?php echo htmlspecialchars($member['staff_id']); ?></p>
                        <p>Email: <?php echo htmlspecialchars($member['email']); ?></p>
                        <p>Department: <?php echo htmlspecialchars($member['department']); ?></p>
                        <p>Position: <?php echo htmlspecialchars($member['position']); ?></p>
                        
                        <!-- Edit Form -->
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                            <div class="form-group">
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($member['full_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <select name="department" required>
                                    <option value="Administration" <?php echo $member['department'] === 'Administration' ? 'selected' : ''; ?>>Administration</option>
                                    <option value="Nursing" <?php echo $member['department'] === 'Nursing' ? 'selected' : ''; ?>>Nursing</option>
                                    <option value="Laboratory" <?php echo $member['department'] === 'Laboratory' ? 'selected' : ''; ?>>Laboratory</option>
                                    <option value="Pharmacy" <?php echo $member['department'] === 'Pharmacy' ? 'selected' : ''; ?>>Pharmacy</option>
                                    <option value="Reception" <?php echo $member['department'] === 'Reception' ? 'selected' : ''; ?>>Reception</option>
                                </select>
                            </div>
                            <button type="submit" class="btn">Update</button>
                        </form>
                        
                        <!-- Delete Form -->
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>
</body>
</html>
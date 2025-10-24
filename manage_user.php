<?php
session_start();
include 'db.php';

// Admin access check
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle Delete User
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_user.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users | Ubwuzu System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
.table thead th { background-color: #1E3A8A; color: white; }
.table tbody tr:hover { background-color: #e9ecef; }
</style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between mb-4">
        <h2>Manage Users</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['role_id'] == 1 ? 'Admin' : 'Customer'; ?></td>
                        <td>
                            <a href="edit_user.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="manage_user.php?delete_id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
            <?php }} else { ?>
                <tr><td colspan="5" class="text-center">No users found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>

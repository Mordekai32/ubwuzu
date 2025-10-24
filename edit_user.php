<?php
session_start();
include 'db.php';

// Admin access check
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Get user ID from URL
if (!isset($_GET['user_id'])) {
    header("Location: manage_user.php");
    exit();
}

$user_id = intval($_GET['user_id']);

// Handle form submission
if (isset($_POST['update_user'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role_id = intval($_POST['role_id']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, role_id = ? WHERE user_id = ?");
    $stmt->bind_param("ssii", $full_name, $email, $role_id, $user_id);
    $stmt->execute();
    $stmt->close();

    $success = "User updated successfully!";
}

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, email, role_id FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $role_id);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User | Ubwuzu System</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<div class="container mx-auto py-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
        <a href="manage_user.php" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">Back to Users</a>
    </div>

    <?php if(isset($success)) { ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?php echo $success; ?>
        </div>
    <?php } ?>

    <div class="bg-white shadow-lg rounded-lg p-8 max-w-lg mx-auto">
        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Role</label>
                <select name="role_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="1" <?php if($role_id == 1) echo 'selected'; ?>>Admin</option>
                    <option value="2" <?php if($role_id == 2) echo 'selected'; ?>>Customer</option>
                </select>
            </div>

            <button type="submit" name="update_user"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                Update User
            </button>
        </form>
    </div>
</div>

</body>
</html>

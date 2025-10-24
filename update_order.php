<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Get order ID
if (!isset($_GET['order_id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch order info
$stmt = $conn->prepare("SELECT o.order_id, o.quantity, o.status, u.full_name, p.product_name 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.user_id 
                        JOIN products p ON o.product_id = p.product_id 
                        WHERE o.order_id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Update order
if (isset($_POST['update_order'])) {
    $quantity = intval($_POST['quantity']);
    $status = $_POST['status'];

    $update_stmt = $conn->prepare("UPDATE orders SET quantity=?, status=? WHERE order_id=?");
    $update_stmt->bind_param("isi", $quantity, $status, $order_id);
    $update_stmt->execute();
    $update_stmt->close();

    $success = "Order updated successfully!";
    // Refresh data
    $stmt = $conn->prepare("SELECT o.order_id, o.quantity, o.status, u.full_name, p.product_name 
                            FROM orders o 
                            JOIN users u ON o.user_id = u.user_id 
                            JOIN products p ON o.product_id = p.product_id 
                            WHERE o.order_id=?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Order | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-gray-50 to-indigo-100 min-h-screen font-sans flex items-center justify-center p-6">

<div class="max-w-2xl w-full bg-white shadow-2xl rounded-3xl p-10 border border-gray-200">
    <h2 class="text-4xl font-bold text-indigo-700 mb-8 text-center">Edit Order Details</h2>

    <?php if(isset($success)) { ?>
        <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg text-center font-semibold shadow-sm">
            <?php echo $success; ?>
        </div>
    <?php } ?>

    <form method="POST" class="space-y-6">
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Customer Name</label>
            <input type="text" value="<?php echo htmlspecialchars($order['full_name']); ?>" disabled
                class="w-full border border-gray-300 bg-gray-100 rounded-xl px-4 py-3 text-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Product</label>
            <input type="text" value="<?php echo htmlspecialchars($order['product_name']); ?>" disabled
                class="w-full border border-gray-300 bg-gray-100 rounded-xl px-4 py-3 text-gray-600 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Quantity</label>
            <input type="number" name="quantity" value="<?php echo htmlspecialchars($order['quantity']); ?>" required
                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-2">Status</label>
            <select name="status" required
                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                <option value="pending" <?php if($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="completed" <?php if($order['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                <option value="canceled" <?php if($order['status'] == 'canceled') echo 'selected'; ?>>Canceled</option>
            </select>
        </div>

        <div class="flex justify-between items-center pt-6">
            <a href="orders.php"
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-xl shadow-md transition duration-200 flex items-center">
                ← Back to Orders
            </a>
            <button type="submit" name="update_order"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-xl shadow-md transition duration-200">
                Save Changes
            </button>
        </div>
    </form>
</div>

</body>
</html>

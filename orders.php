<?php
session_start();
include 'db.php';

// Check admin access
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle delete order
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM orders WHERE order_id=$delete_id");
    $success = "Order deleted successfully!";
}

// Fetch all orders with user & product info
$query = "SELECT o.order_id, o.quantity, o.status, o.admin_response, 
                 u.full_name, u.email, u.phone, p.product_name, p.price 
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          JOIN products p ON o.product_id = p.product_id 
          ORDER BY o.order_id DESC";
$orders_res = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Orders | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<div class="container mx-auto py-12 px-4">
    <div class="max-w-6xl mx-auto bg-white shadow-xl rounded-2xl p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Manage Orders</h2>

        <?php if(isset($success)) { ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto bg-white rounded-xl shadow-lg">
                <thead class="bg-indigo-600 text-white rounded-t-xl">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Customer</th>
                        <th class="px-4 py-2 text-left">Phone</th> <!-- New column -->
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Product</th>
                        <th class="px-4 py-2 text-left">Quantity</th>
                        <th class="px-4 py-2 text-left">Price</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Response</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    if($orders_res && $orders_res->num_rows > 0){
                        $i = 1;
                        while($order = $orders_res->fetch_assoc()){ ?>
                            <tr>
                                <td class="px-4 py-2"><?php echo $i++; ?></td>
                                <td class="px-4 py-2 font-semibold text-gray-700"><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td class="px-4 py-2 text-gray-600"><?php echo htmlspecialchars($order['phone']); ?></td> <!-- Phone -->
                                <td class="px-4 py-2 text-gray-600"><?php echo htmlspecialchars($order['email']); ?></td>
                                <td class="px-4 py-2 text-gray-700"><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td class="px-4 py-2"><?php echo $order['quantity']; ?></td>
                                <td class="px-4 py-2"><?php echo $order['price']; ?></td>
                                <td class="px-4 py-2 capitalize text-gray-800 font-semibold">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </td>
                                <td class="px-4 py-2 text-gray-600">
                                    <?php echo htmlspecialchars($order['admin_response']); ?>
                                </td>
                                <td class="px-4 py-2 flex space-x-2">
                                    <a href="update_order.php?order_id=<?php echo $order['order_id']; ?>" 
                                       class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
                                       Edit
                                    </a>
                                    <a href="send_response.php?order_id=<?php echo $order['order_id']; ?>" 
                                       class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
                                       Send Response
                                    </a>
                                    <a href="?delete_id=<?php echo $order['order_id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this order?')" 
                                       class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="10" class="px-4 py-2 text-gray-500 text-center">No orders found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="admin_dashboard.php"
               class="inline-block w-full text-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded-xl shadow-lg transition duration-200">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
</body>
</html>

<?php
session_start();
include 'db.php';

// Admin access check
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch dashboard counts
$users = $products = $orders = $ads = 0;

if ($conn) {
    $res = $conn->query("SELECT COUNT(*) AS total FROM users");
    $users = $res ? $res->fetch_assoc()['total'] : 0;

    $res = $conn->query("SELECT COUNT(*) AS total FROM products");
    $products = $res ? $res->fetch_assoc()['total'] : 0;

    $res = $conn->query("SELECT COUNT(*) AS total FROM orders");
    $orders = $res ? $res->fetch_assoc()['total'] : 0;

    $res = $conn->query("SELECT COUNT(*) AS total FROM advertisements");
    $ads = $res ? $res->fetch_assoc()['total'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Ubwuzu System</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
.sidebar { background: linear-gradient(180deg,#1E3A8A,#3B82F6); min-height:100vh; padding:2rem 1rem; color:white; position: fixed; width: 250px; }
.sidebar a { display:block; padding:0.75rem 1rem; margin-bottom:0.25rem; border-radius:0.5rem; text-decoration:none; transition:0.3s; }
.sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.2); }
.card { border-radius: 1rem; box-shadow:0 4px 10px rgba(0,0,0,0.1); padding:1.5rem; background:white; }
.small-chart { height: 300px; } /* Height for graphs */
footer a { transition: 0.3s; }
footer a:hover { color: #3B82F6; }
</style>
</head>
<body class="flex flex-col min-h-screen">

<!-- Sidebar -->
<div class="sidebar">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-rocket mr-2"></i>Ubwuzu Admin</h2>
    <a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
    <a href="products.php"><i class="fas fa-box-seam mr-2"></i>Products</a>
    <a href="ads.php"><i class="fas fa-bullhorn mr-2"></i>Advertisements</a>
    <a href="orders.php"><i class="fas fa-shopping-cart mr-2"></i>Orders</a>
    <a href="manage_user.php"><i class="fas fa-users mr-2"></i>Manage Users</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="flex-grow ml-64 p-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Welcome, <?php echo $_SESSION['full_name']; ?> 👋</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card flex flex-col items-center">
            <i class="fas fa-users text-4xl text-blue-500 mb-3"></i>
            <h3 class="text-lg font-semibold">Total Users</h3>
            <p class="text-2xl font-bold"><?php echo $users; ?></p>
        </div>
        <div class="card flex flex-col items-center">
            <i class="fas fa-box-seam text-4xl text-green-500 mb-3"></i>
            <h3 class="text-lg font-semibold">Total Products</h3>
            <p class="text-2xl font-bold"><?php echo $products; ?></p>
        </div>
        <div class="card flex flex-col items-center">
            <i class="fas fa-shopping-cart text-4xl text-yellow-500 mb-3"></i>
            <h3 class="text-lg font-semibold">Total Orders</h3>
            <p class="text-2xl font-bold"><?php echo $orders; ?></p>
        </div>
        <div class="card flex flex-col items-center">
            <i class="fas fa-bullhorn text-4xl text-red-500 mb-3"></i>
            <h3 class="text-lg font-semibold">Total Ads</h3>
            <p class="text-2xl font-bold"><?php echo $ads; ?></p>
        </div>
    </div>

    <!-- Small Graphs -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="card">
            <h2 class="text-lg font-semibold mb-2">Orders Overview</h2>
            <canvas id="ordersChart" class="small-chart w-full"></canvas>
        </div>
        <div class="card">
            <h2 class="text-lg font-semibold mb-2">Products Overview</h2>
            <canvas id="productsChart" class="small-chart w-full"></canvas>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-900 text-white mt-auto py-10">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <h4 class="text-lg font-semibold mb-2">Ubwuzu System</h4>
            <p class="text-gray-400 text-sm">Powering your digital marketplace with insights & control.</p>
        </div>
        <div>
            <h4 class="text-lg font-semibold mb-2">Quick Links</h4>
            <ul class="space-y-1 text-sm">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="manage_user.php">Users</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-lg font-semibold mb-2">Contact</h4>
            <ul class="space-y-1 text-sm">
                <li>Email: support@ubwuzu.com</li>
                <li>Phone: +250 788 000 000</li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    <div class="text-center text-gray-500 mt-8 text-sm">&copy; <?php echo date('Y'); ?> Ubwuzu System. All rights reserved.</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Orders Chart
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{ 
            label:'Orders', 
            data:[12,19,15,22,30,28], 
            borderColor:'rgba(255,193,7,1)',
            backgroundColor:'rgba(255,193,7,0.2)',
            fill:true,
            tension:0.3 
        }]
    },
    options:{ responsive:true, maintainAspectRatio:false }
});

// Products Chart
const productsCtx = document.getElementById('productsChart').getContext('2d');
new Chart(productsCtx, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{ 
            label:'Products', 
            data:[10,14,18,25,27,33], 
            borderColor:'rgba(40,167,69,1)',
            backgroundColor:'rgba(40,167,69,0.2)',
            fill:true,
            tension:0.3 
        }]
    },
    options:{ responsive:true, maintainAspectRatio:false }
});
</script>
</body>
</html>

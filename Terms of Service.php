<?php
session_start();
include 'db.php';

// Admin access check
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms of Service | Ubwuzu System</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans min-h-screen">

<!-- Container -->
<div class="container mx-auto py-12 px-4 max-w-4xl">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Terms of Service</h1>
        <p class="mb-4 text-gray-700">
            Welcome to <span class="font-semibold">Ubwuzu System</span>. These Terms of Service (“Terms”) govern your access to and use of the admin dashboard and related services. By using our platform, you agree to comply with these terms.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">1. Admin Account</h2>
        <p class="text-gray-700 mb-4">
            Admin accounts are provided for managing the platform, including products, users, orders, and advertisements. Admin credentials must be kept secure and confidential.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">2. Responsibilities</h2>
        <p class="text-gray-700 mb-4">
            Admins are responsible for maintaining the integrity of the system, ensuring accurate data management, and following all company policies and legal requirements.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">3. Data Management</h2>
        <p class="text-gray-700 mb-4">
            Admins may access sensitive user data. This data must be handled responsibly, securely stored, and used solely for platform administration purposes.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">4. Prohibited Actions</h2>
        <ul class="list-disc ml-6 text-gray-700 mb-4">
            <li>Unauthorized sharing of admin credentials.</li>
            <li>Modifying, deleting, or accessing data without proper authorization.</li>
            <li>Any activity that compromises system security or performance.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">5. Updates to Terms</h2>
        <p class="text-gray-700 mb-4">
            We may update these Terms periodically. Admins will be notified of any changes, and continued use of the dashboard implies acceptance of the updated Terms.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">6. Contact</h2>
        <p class="text-gray-700 mb-4">
            For questions regarding these Terms, contact us at <span class="text-indigo-600 font-semibold">mordekai893@gmail.com</span>.
        </p>

        <!-- Back to Admin Dashboard Button -->
        <div class="mt-8">
            <a href="admin_dashboard.php"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition duration-200">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

</body>
</html>

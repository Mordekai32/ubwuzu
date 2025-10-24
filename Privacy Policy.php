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
<title>Privacy Policy | Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans min-h-screen">

<!-- Container -->
<div class="container mx-auto py-12 px-4 max-w-4xl">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Privacy Policy</h1>
        <p class="mb-4 text-gray-700">At <span class="font-semibold">Ubwuzu System</span>, we value your privacy and are committed to protecting any information collected through our platform.</p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">1. Information Collection</h2>
        <p class="text-gray-700 mb-4">
            We collect personal information such as names, email addresses, and account activity when users register or interact with our system. This data helps us provide and improve our services.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">2. Data Usage</h2>
        <p class="text-gray-700 mb-4">
            Collected information is used for account management, order processing, and communication with users. We do not sell or rent your data to third parties.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">3. Data Security</h2>
        <p class="text-gray-700 mb-4">
            We implement industry-standard security measures to safeguard your information. Access is limited to authorized personnel only.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">4. Cookies</h2>
        <p class="text-gray-700 mb-4">
            Our system uses cookies to enhance the user experience, track login sessions, and improve functionality.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">5. User Rights</h2>
        <p class="text-gray-700 mb-4">
            Users have the right to access, modify, or delete their personal information. Requests can be sent to our support team at <span class="text-indigo-600 font-semibold">mordekai893@gmail.com</span>.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">6. Changes to This Policy</h2>
        <p class="text-gray-700 mb-4">
            We may update our Privacy Policy periodically. Updated policies will be posted here with the effective date.
        </p>

        <!-- Back to Dashboard Button -->
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

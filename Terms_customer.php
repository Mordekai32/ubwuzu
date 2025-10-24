<?php
session_start();
include 'db.php';

// Customer access check
if (!isset($_SESSION['user_id'])) {
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
            Welcome to <span class="font-semibold">Ubwuzu System</span>. These Terms of Service (“Terms”) govern your use of our platform, including browsing products, placing orders, and interacting with advertisements. By using our services, you agree to these Terms.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">1. Account Responsibilities</h2>
        <p class="text-gray-700 mb-4">
            Customers are responsible for maintaining the confidentiality of their account credentials and for all activity that occurs under their account.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">2. Orders and Payments</h2>
        <p class="text-gray-700 mb-4">
            All orders must be placed through the platform. Prices and availability are subject to change. Payments must be completed through the authorized payment methods.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">3. Prohibited Activities</h2>
        <ul class="list-disc ml-6 text-gray-700 mb-4">
            <li>Placing fraudulent orders or using stolen payment methods.</li>
            <li>Attempting to hack or interfere with the platform.</li>
            <li>Misusing the system or disrupting services for other users.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">4. Privacy and Data</h2>
        <p class="text-gray-700 mb-4">
            Your data is handled according to our Privacy Policy. By using our platform, you consent to the collection and use of data for order processing and service improvement.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">5. Changes to Terms</h2>
        <p class="text-gray-700 mb-4">
            We may update these Terms from time to time. Continued use of the platform indicates acceptance of the revised Terms.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">6. Contact</h2>
        <p class="text-gray-700 mb-4">
            For any questions about these Terms, please contact us at <span class="text-indigo-600 font-semibold">mordekai893@gmail.com</span>.
        </p>

        <!-- Back to Dashboard Button -->
        <div class="mt-8">
            <a href="customer_dashboard.php"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition duration-200">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

</body>
</html>

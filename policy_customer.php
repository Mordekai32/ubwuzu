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
<title>Privacy Policy | Ubwuzu System</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans min-h-screen">

<!-- Container -->
<div class="container mx-auto py-12 px-4 max-w-4xl">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Privacy Policy</h1>
        <p class="mb-4 text-gray-700">At <span class="font-semibold">Ubwuzu System</span>, your privacy is our priority. This policy explains how we collect, use, and protect your information when you use our services.</p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">1. Information We Collect</h2>
        <p class="text-gray-700 mb-4">
            We collect information such as your name, email address, and order details when you interact with our platform. This helps us deliver a smooth and personalized experience.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">2. How We Use Your Data</h2>
        <p class="text-gray-700 mb-4">
            Your data is used to process orders, communicate important updates, and improve our services. We do not sell your personal information to third parties.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">3. Data Protection</h2>
        <p class="text-gray-700 mb-4">
            We implement strict security measures to protect your personal information and ensure only authorized personnel have access.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">4. Cookies</h2>
        <p class="text-gray-700 mb-4">
            Our website uses cookies to maintain your session, track preferences, and improve the overall experience.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">5. Your Rights</h2>
        <p class="text-gray-700 mb-4">
            You can access, update, or delete your personal data at any time. Contact us at <span class="text-indigo-600 font-semibold">mordekai893@gmail.com</span> for any requests.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-3">6. Updates to This Policy</h2>
        <p class="text-gray-700 mb-4">
            We may update this policy occasionally. The latest version will always be available here with the effective date.
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

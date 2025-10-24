<?php
session_start();
include 'db.php';

// Admin access check
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Check if ad ID is provided
if(!isset($_GET['ad_id'])){
    header("Location: manage_advert.php");
    exit();
}

$ad_id = intval($_GET['ad_id']);

// Handle form submission
if(isset($_POST['update_ad'])){
    $title = trim($_POST['ad_title']);
    $content = trim($_POST['ad_content']);

    $stmt = $conn->prepare("UPDATE advertisements SET ad_title=?, ad_content=? WHERE ad_id=?");
    $stmt->bind_param("ssi", $title, $content, $ad_id);
    $stmt->execute();
    $stmt->close();

    $success = "Advertisement updated successfully!";
}

// Fetch ad details
$stmt = $conn->prepare("SELECT ad_title, ad_content FROM advertisements WHERE ad_id=?");
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$stmt->bind_result($ad_title, $ad_content);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Advertisement | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<div class="container mx-auto py-12 px-4">
    <div class="max-w-2xl mx-auto bg-white shadow-xl rounded-2xl p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Edit Advertisement</h2>

        <?php if(isset($success)) { ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Title</label>
                <input type="text" name="ad_title" value="<?php echo htmlspecialchars($ad_title); ?>" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Content</label>
                <textarea name="ad_content" rows="4" required
                          class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200"><?php echo htmlspecialchars($ad_content); ?></textarea>
            </div>

            <button type="submit" name="update_ad"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg transition duration-200">
                Update Advertisement
            </button>
        </form>

        <!-- Back to manage adverts -->
        <div class="mt-6">
            <a href="ads.php"
               class="inline-block w-full text-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded-xl shadow-lg transition duration-200">
                Back to Manage Advertisements
            </a>
        </div>
    </div>
</div>

</body>
</html>

<?php
session_start();
include 'db.php';

// Admin access check
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle Add Advertisement
if(isset($_POST['add_ad'])){
    $title = trim($_POST['ad_title']);
    $content = trim($_POST['ad_content']);

    $stmt = $conn->prepare("INSERT INTO advertisements (ad_title, ad_content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();

    $success = "Advertisement added successfully!";
}

// Handle Delete Advertisement
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM advertisements WHERE ad_id = $delete_id");
    $success = "Advertisement deleted successfully!";
}

// Fetch all advertisements
$ads_res = $conn->query("SELECT * FROM advertisements ORDER BY ad_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Advertisements | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<div class="container mx-auto py-12 px-4">

    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Manage Advertisements</h2>

        <?php if(isset($success)) { ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <!-- Add Advertisement Form -->
        <form method="POST" class="space-y-6 mb-8">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Title</label>
                <input type="text" name="ad_title" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Content</label>
                <textarea name="ad_content" rows="4" required
                          class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200"></textarea>
            </div>
            <button type="submit" name="add_ad"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg transition duration-200">
                Add Advertisement
            </button>
        </form>

        <!-- List of Advertisements -->
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Existing Advertisements</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto bg-white rounded-xl shadow-lg">
                <thead class="bg-indigo-600 text-white rounded-t-xl">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2 text-left">Content</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    if($ads_res && $ads_res->num_rows>0){
                        $i = 1;
                        while($ad = $ads_res->fetch_assoc()){ ?>
                            <tr>
                                <td class="px-4 py-2"><?php echo $i++; ?></td>
                                <td class="px-4 py-2 font-semibold text-gray-700"><?php echo htmlspecialchars($ad['ad_title']); ?></td>
                                <td class="px-4 py-2 text-gray-600"><?php echo htmlspecialchars($ad['ad_content']); ?></td>
                                <td class="px-4 py-2 space-x-2">
                                    <a href="edit_ad.php?ad_id=<?php echo $ad['ad_id']; ?>"
                                       class="px-3 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg">Edit</a>
                                    <a href="?delete_id=<?php echo $ad['ad_id']; ?>" onclick="return confirm('Are you sure?')"
                                       class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg">Delete</a>
                                </td>
                            </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-gray-500 text-center">No advertisements found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Back to Dashboard -->
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

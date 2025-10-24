<?php
session_start();
include 'db.php';

// Admin access check
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);

    // Handle image upload
    $image = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array(strtolower($ext), $allowed)) {
            $image = uniqid() . '.' . $ext;
            $upload_dir = __DIR__ . '/uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_dir . $image);
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (product_name, price, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $name, $price, $image);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Product
if (isset($_GET['delete_product'])) {
    $id = intval($_GET['delete_product']);
    $res = $conn->query("SELECT image FROM products WHERE product_id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $image_path = __DIR__ . '/uploads/' . $row['image'];
        if ($row['image'] && file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Products
$products = $conn->query("SELECT * FROM products ORDER BY product_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Manage Products</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<div class="container mx-auto py-12 px-4">
    <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-2xl p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Manage Products</h2>

        <!-- Add Product Form -->
        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <input type="text" name="product_name" placeholder="Product Name" required 
                class="border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="number" name="price" placeholder="Price" required step="0.01"
                class="border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="file" name="product_image" accept="image/*"
                class="border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" name="add_product"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl shadow-lg transition duration-200">
                Add Product
            </button>
        </form>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-xl shadow-lg">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">#</th>
                        <th class="py-3 px-4 text-left">Product Name</th>
                        <th class="py-3 px-4 text-left">Price</th>
                        <th class="py-3 px-4 text-left">Image</th>
                        <th class="py-3 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    <?php if ($products && $products->num_rows > 0) { $i=1;
                        while ($p = $products->fetch_assoc()) { ?>
                        <tr class="border-b hover:bg-gray-100 transition duration-200">
                            <td class="py-3 px-4"><?php echo $i++; ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($p['product_name']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($p['price']); ?></td>
                            <td class="py-3 px-4">
                                <?php if ($p['image']) { ?>
                                    <img src="uploads/<?php echo htmlspecialchars($p['image']); ?>" alt=""
                                         class="w-20 h-20 object-cover rounded">
                                <?php } else { echo 'No image'; } ?>
                            </td>
                            <td class="py-3 px-4 space-x-2">
                                <a href="edit_product.php?product_id=<?php echo $p['product_id']; ?>"
                                   class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded-lg transition duration-200">Edit</a>
                                <a href="?delete_product=<?php echo $p['product_id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this product?');"
                                   class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-lg transition duration-200">Delete</a>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">No products found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="admin_dashboard.php"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition duration-200">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

</body>
</html>

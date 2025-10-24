<?php
session_start();
include 'db.php';

// Admin access check
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Get product ID from URL
if (!isset($_GET['product_id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['product_id']);

// Fetch product details
$stmt = $conn->prepare("SELECT product_name, price FROM products WHERE product_id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($product_name, $price);
$stmt->fetch();
$stmt->close();

// Handle form submission
if (isset($_POST['update_product'])) {
    $new_name = trim($_POST['product_name']);
    $new_price = floatval($_POST['price']);
    
    $stmt = $conn->prepare("UPDATE products SET product_name=?, price=? WHERE product_id=?");
    $stmt->bind_param("sdi", $new_name, $new_price, $product_id);
    $stmt->execute();
    $stmt->close();

    $success = "Product updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<div class="container mx-auto py-12 px-4">
    <div class="max-w-2xl mx-auto bg-white shadow-xl rounded-2xl p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Edit Product</h2>

        <?php if(isset($success)) { ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Product Name</label>
                <input type="text" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Price</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($price); ?>" step="0.01" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" name="update_product"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg transition duration-200">
                Update Product
            </button>
        </form>

        <!-- Back to Products Button -->
        <div class="mt-6">
            <a href="products.php"
               class="w-full inline-block text-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded-xl shadow-lg transition duration-200">
               Back to Products
            </a>
        </div>
    </div>
</div>

</body>
</html>

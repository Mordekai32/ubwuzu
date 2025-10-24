<?php
include 'db.php';

// Fetch products
$products = $conn->query("SELECT * FROM products ORDER BY product_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<div class="container mx-auto py-12 px-4">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Products</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <?php if ($products && $products->num_rows > 0) {
            while ($p = $products->fetch_assoc()) { ?>
            <div class="bg-white shadow-lg rounded-2xl p-4 flex flex-col items-center">
                <?php if ($p['image']) { ?>
                    <img src="uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="" class="w-40 h-40 object-cover rounded-xl mb-4">
                <?php } ?>
                <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($p['product_name']); ?></h3>
                <p class="text-gray-700 mb-4">$<?php echo htmlspecialchars($p['price']); ?></p>
            </div>
        <?php }} else { ?>
            <p class="text-gray-500 col-span-3 text-center">No products found.</p>
        <?php } ?>
    </div>
</div>

</body>
</html>

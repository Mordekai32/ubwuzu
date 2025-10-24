<?php
session_start();
include 'db.php';

// ------------------ Check Login ------------------
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ------------------ Profile Picture Upload ------------------
$profile_success = '';
if (isset($_POST['upload_profile'])) {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $file_name = $_FILES['profile_pic']['name'];
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (in_array($file_ext, $allowed)) {
            $new_name = 'profile_'.$user_id.'_'.time().'.'.$file_ext;
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $destination = $upload_dir.$new_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE user_id=?");
                if(!$stmt){ die("Prepare failed: ".$conn->error); }
                $stmt->bind_param("si", $new_name, $user_id);
                $stmt->execute();
                $stmt->close();
                $_SESSION['profile_pic'] = $new_name;
                $profile_success = "✅ Profile picture uploaded successfully!";
            } else {
                $profile_success = "❌ Failed to upload image.";
            }
        } else {
            $profile_success = "⚠️ Only JPG, PNG, GIF files allowed.";
        }
    }
}

// ------------------ Place Order + Automatic Reply ------------------
$order_success = '';
if (isset($_POST['place_order'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // 1️⃣ Insert Order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'pending')");
    if(!$stmt){ die("Prepare failed: ".$conn->error); }
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // 2️⃣ Insert Automatic Admin Message (optional)
    $admin_msg = "Customer placed an order for Product ID: $product_id, Quantity: $quantity.";
    $stmt2 = $conn->prepare("UPDATE orders SET admin_response=? WHERE order_id=?");
    if(!$stmt2){ die("Prepare failed: ".$conn->error); }
    $stmt2->bind_param("si", $admin_msg, $order_id);
    $stmt2->execute();
    $stmt2->close();

    // 3️⃣ Insert Automatic Customer Reply
    $auto_reply = "✅ Thank you for your message regarding Product ID: $product_id. We received it!";
    $stmt3 = $conn->prepare("UPDATE orders SET customer_response=? WHERE order_id=?");
    if(!$stmt3){ die("Prepare failed: ".$conn->error); }
    $stmt3->bind_param("si", $auto_reply, $order_id);
    $stmt3->execute();
    $stmt3->close();

    $order_success = "🎉 Order placed and automatic reply sent!";
}

// ------------------ Fetch Products ------------------
$products_res = $conn->query("SELECT * FROM products ORDER BY product_id DESC");

// ------------------ Fetch Messages ------------------
$msg_res = $conn->query("
    SELECT o.*, p.product_name 
    FROM orders o 
    JOIN products p ON o.product_id = p.product_id
    WHERE o.user_id = $user_id AND o.admin_response IS NOT NULL
    ORDER BY o.created_at DESC
");

// ------------------ Profile Picture ------------------
$profile_pic = $_SESSION['profile_pic'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Dashboard | Automatic Reply</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: linear-gradient(135deg, #1E3A8A, #3B82F6); color: #333; font-family: 'Poppins', sans-serif; }
.navbar { background: white !important; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
.navbar-brand { font-weight: 700; color: #1E3A8A !important; }
.card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; border-radius: 12px; }
.card-hover:hover { transform: scale(1.03); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
.profile-pic { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #2563EB; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light p-3">
  <div class="container">
    <a class="navbar-brand" href="#">Ubwuzu System</a>
    <div class="d-flex align-items-center">
      <?php if($profile_pic){ ?>
        <img src="uploads/<?php echo htmlspecialchars($profile_pic); ?>" class="profile-pic me-2" alt="Profile">
      <?php } ?>
      <span class="me-3 fw-semibold text-secondary">👋 Hello, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-5">

  <!-- Profile Upload -->
  <div class="mb-4">
    <h5 class="fw-bold text-white">Upload Profile Picture</h5>
    <?php if($profile_success){ ?>
      <div class="alert alert-info mt-2"><?php echo $profile_success; ?></div>
    <?php } ?>
    <form method="POST" enctype="multipart/form-data" class="d-flex gap-3">
      <input type="file" name="profile_pic" class="form-control" accept="image/*" required>
      <button type="submit" name="upload_profile" class="btn btn-primary">Upload</button>
    </form>
  </div>

  <!-- Place Order -->
  <?php if ($order_success) { ?>
    <div class="alert alert-success"><?php echo $order_success; ?></div>
  <?php } ?>
  <h3 class="fw-bold text-white mb-4">🛍️ Available Products</h3>
  <div class="row g-4">
    <?php if ($products_res && $products_res->num_rows > 0) {
        while ($p = $products_res->fetch_assoc()) { ?>
        <div class="col-md-4">
          <div class="card card-hover">
            <?php if($p['image']){ ?>
              <img src="uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>" class="card-img-top" style="height:200px; object-fit:cover; border-radius:12px 12px 0 0;">
            <?php } ?>
            <div class="card-body">
              <h5 class="fw-semibold text-dark"><?php echo htmlspecialchars($p['product_name']); ?></h5>
              <p class="text-secondary mb-3">Price: <strong><?php echo htmlspecialchars($p['price']); ?> RWF</strong></p>
              <form method="POST">
                <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                <input type="number" name="quantity" value="1" min="1" class="form-control mb-2" required>
                <button type="submit" name="place_order" class="btn btn-primary w-100">Place Order</button>
              </form>
            </div>
          </div>
        </div>
    <?php }} ?>
  </div>

  <!-- Messages -->
  <div class="mt-5">
    <h3 class="fw-bold text-white mb-4">💌 Messages</h3>
    <?php if($msg_res && $msg_res->num_rows > 0) {
        while($msg = $msg_res->fetch_assoc()){ ?>
        <div class="card card-hover mb-3 p-3 bg-white">
            <h6 class="fw-bold"><?php echo htmlspecialchars($msg['product_name']); ?></h6>
            <p><b>Admin:</b> <?php echo nl2br(htmlspecialchars($msg['admin_response'])); ?></p>
            <p><b>Your reply:</b> <?php echo nl2br(htmlspecialchars($msg['customer_response'])); ?></p>
        </div>
    <?php }} else { ?>
        <div class="text-white">No messages yet.</div>
    <?php } ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include 'db.php';

$user_logged_in = isset($_SESSION['user_id']);
$user_id = $user_logged_in ? $_SESSION['user_id'] : null;

// Handle Profile Picture Upload
$profile_success = '';
if ($user_logged_in && isset($_POST['upload_profile'])) {
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
                $stmt->bind_param("si", $new_name, $user_id);
                $stmt->execute();
                $stmt->close();
                $profile_success = "✅ Profile picture uploaded successfully!";
                $_SESSION['profile_pic'] = $new_name;
            } else {
                $profile_success = "❌ Failed to upload image.";
            }
        } else {
            $profile_success = "⚠️ Only JPG, PNG, GIF files allowed.";
        }
    }
}

// Handle Order Submission
$order_success = '';
if ($user_logged_in && isset($_POST['place_order'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
    $stmt->close();
    $order_success = "🎉 Order placed successfully!";
}

// Fetch Products
$products_res = $conn->query("SELECT * FROM products ORDER BY product_id DESC");

// Fetch Advertisements
$advert_res = $conn->query("SELECT * FROM advertisements ORDER BY created_at DESC");

// Fetch Admin Messages
$msg_res = [];
if($user_logged_in){
    $msg_res = $conn->query("
        SELECT o.*, p.product_name 
        FROM orders o 
        JOIN products p ON o.product_id = p.product_id
        WHERE o.user_id = $user_id AND o.admin_response IS NOT NULL
        ORDER BY o.created_at DESC
    ");
}

$profile_pic = $_SESSION['profile_pic'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Customer Dashboard | Ubwuzu System<img src="a.jpeg" alt="Ubwuzu Logo" class="logo animate__animated animate__zoomIn animate__delay-1s"></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: linear-gradient(135deg, #1E3A8A, #3B82F6); color: #333; font-family: 'Poppins', sans-serif; }
.navbar { background: white !important; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
.navbar-brand { font-weight: 700; color: #1E3A8A !important; }
.card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; border: none; border-radius: 12px; }
.card-hover:hover { transform: scale(1.03); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
.profile-pic { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #2563EB; }
footer { background: #0f172a; color: #cbd5e1; padding-top: 2rem; margin-top: 3rem; }
footer a { color: #60a5fa; text-decoration: none; }
footer a:hover { color: #fff; text-decoration: underline; }
.instagram { background: linear-gradient(90deg, #d946ef, #ec4899, #f97316); color: white; border-radius: 12px; padding: 10px 25px; display: inline-block; margin-top: 10px; animation: pulseInsta 2s infinite alternate; }
@keyframes pulseInsta { from { transform: scale(1); box-shadow: 0 0 10px rgba(255,255,255,0.3); } to { transform: scale(1.05); box-shadow: 0 0 25px rgba(255,255,255,0.6); } }
.btn-blue { background: linear-gradient(135deg, #2563EB, #3B82F6); color: white; border: none; border-radius: 50px; padding: 8px 20px; font-weight: 500; transition: 0.3s; }
.btn-blue:hover { background: linear-gradient(135deg, #1E40AF, #2563EB); transform: scale(1.05); }
.btn-darkk { background: linear-gradient(135deg, #111827, #1F2937); color: white; border: none; border-radius: 50px; padding: 8px 20px; font-weight: 500; transition: 0.3s; }
.btn-darkk:hover { background: linear-gradient(135deg, #1F2937, #374151); transform: scale(1.05); }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light animate__animated animate__fadeInDown">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fa-solid fa-droplet text-info"></i> Ubwuzu System</a>

    <div class="ms-auto d-flex align-items-center gap-2">
      <?php if(!$user_logged_in): ?>
        <a href="register.php" class="btn btn-blue"><i class="fa-solid fa-user-plus me-1"></i> Register</a>
        <a href="login.php" class="btn btn-darkk"><i class="fa-solid fa-right-to-bracket me-1"></i> Login</a>
      <?php else: ?>
        <?php if($profile_pic){ ?>
          <img src="uploads/<?php echo htmlspecialchars($profile_pic); ?>" class="profile-pic ms-3" alt="Profile">
        <?php } ?>
        <span class="ms-2 fw-semibold text-secondary">
          👋 Hello, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </span>
        <a href="logout.php" class="btn btn-danger btn-sm px-3 ms-2 animate__animated animate__bounceIn">
          <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-5">
  <?php if($profile_success){ ?>
    <div class="alert alert-info"><?php echo $profile_success; ?></div>
  <?php } ?>
  <?php if($order_success){ ?>
    <div class="alert alert-success"><?php echo $order_success; ?></div>
  <?php } ?>

  <div class="mb-5" data-aos="fade-down">
    <h3 class="fw-bold text-white mb-4">📢 Latest Adverts</h3>
    <div class="row g-4">
      <?php if($advert_res && $advert_res->num_rows>0): while($ad=$advert_res->fetch_assoc()): ?>
        <div class="col-md-4" data-aos="flip-left">
          <div class="card card-hover bg-white shadow-sm" style="border-radius:12px;">
            <div class="card-body">
              <h5 class="fw-bold text-dark"><?php echo htmlspecialchars($ad['ad_title']); ?></h5>
              <p class="text-secondary mb-2"><small><?php echo date('d M Y', strtotime($ad['created_at'])); ?></small></p>
              <p class="text-dark"><?php echo nl2br(htmlspecialchars($ad['ad_content'])); ?></p>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <div class="text-white">No adverts available right now.</div>
      <?php endif; ?>
    </div>
  </div>

  <div data-aos="fade-up">
    <h3 class="fw-bold text-white mb-4">🛍️ Available Products</h3>
    <div class="row g-4">
      <?php if($products_res && $products_res->num_rows>0): while($p=$products_res->fetch_assoc()): ?>
        <div class="col-md-4" data-aos="flip-left">
          <div class="card card-hover">
            <?php if($p['image']): ?>
              <img src="uploads/<?php echo htmlspecialchars($p['image']); ?>" class="card-img-top" style="height:200px; object-fit:cover; border-radius:12px 12px 0 0;">
            <?php endif; ?>
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <h5 class="fw-semibold text-dark"><?php echo htmlspecialchars($p['product_name']); ?></h5>
                <p class="text-secondary mb-3">Price: <strong><?php echo htmlspecialchars($p['price']); ?> RWF</strong></p>
              </div>
              <?php if($user_logged_in): ?>
                <form method="POST">
                  <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                  <input type="number" name="quantity" value="1" min="1" class="form-control mb-2" required>
                  <button type="submit" name="place_order" class="btn btn-primary w-100">Place Order</button>
                </form>
              <?php else: ?>
                <button class="btn btn-warning w-100" disabled>Login to Order</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <div class="text-center text-white">No products available right now.</div>
      <?php endif; ?>
    </div>
  </div>

  <?php if($user_logged_in): ?>
  <div class="mt-5" data-aos="fade-up">
    <h3 class="fw-bold text-white mb-4">💌 Messages from Admin</h3>
    <?php if($msg_res && $msg_res->num_rows>0): while($msg=$msg_res->fetch_assoc()): ?>
      <div class="card card-hover mb-3 p-3 bg-white rounded-xl shadow-sm">
        <div class="d-flex justify-content-between">
          <h6 class="fw-bold text-dark"><?php echo htmlspecialchars($msg['product_name']); ?></h6>
          <span class="text-secondary small"><?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></span>
        </div>
        <p class="text-gray-700 mb-1">Status: <b><?php echo htmlspecialchars(ucfirst($msg['status'])); ?></b></p>
        <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($msg['admin_response'])); ?></p>
      </div>
    <?php endwhile; else: ?>
      <div class="text-white">No messages from admin yet.</div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<footer class="text-center">
  <p class="mt-3">Follow us for new updates:</p>
  <a href="https://www.instagram.com/M.blaise_320" target="_blank" class="instagram me-2">
    <i class="fab fa-instagram"></i> @M.blaise_320
  </a>
  <a href="https://www.facebook.com/UMMordekai" target="_blank" class="btn btn-primary ms-2" style="border-radius:12px;">
    <i class="fab fa-facebook-f"></i> UM Mordekai
  </a>
  <div class="mt-4 text-secondary small">
    <p class="mb-1">
      📧 <a href="mailto:mordekai893@gmail.com" class="text-info">mordekai893@gmail.com</a> |
      ☎️ <a href="tel:+250796381024" class="text-info">+250 796 381 024</a>
    </p>
    <p class="mb-2">
      <a href="Terms_customer.php" class="text-decoration-none text-info me-3">Terms of Service</a>
      <a href="policy_customer.php" class="text-decoration-none text-info">Privacy Policy</a>
    </p>
    <div class="small text-secondary">
      &copy; <?php echo date('Y'); ?> <b>Ubwuzu System</b> | Designed by <b>Mordekai</b>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ duration: 1200, once: true });</script>
</body>
</html>

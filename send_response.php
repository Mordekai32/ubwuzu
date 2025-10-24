<?php
session_start();
include 'db.php';

// Only admin access
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $order = $conn->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id=u.user_id WHERE order_id=$order_id")->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = trim($_POST['status']);
        $response = trim($_POST['response']);

        $sql = "UPDATE orders SET status=?, admin_response=? WHERE order_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $response, $order_id);
        $stmt->execute();

        $success = "✅ Response sent successfully!";
        // Refresh updated order info
        $order = $conn->query("SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id=u.user_id WHERE order_id=$order_id")->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Send Response | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #4f46e5, #3b82f6);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
}
.card-custom {
    border-radius: 25px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    overflow: hidden;
}
.card-header-gradient {
    background: linear-gradient(135deg, #6366f1, #2563eb);
    color: white;
    text-align: center;
    padding: 1.5rem;
    font-size: 1.5rem;
    font-weight: bold;
}
.btn-hover:hover {
    transform: scale(1.05);
    transition: 0.3s;
}
</style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-custom">
                <div class="card-header-gradient">
                    Send Response
                </div>
                <div class="card-body p-5">
                    <p class="text-center text-muted mb-4">Order #<?php echo $order['order_id']; ?> - <?php echo htmlspecialchars($order['full_name']); ?></p>

                    <?php if(isset($success)) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>

                    <form method="POST" class="mb-3">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Customer</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($order['full_name']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Approved" <?php if($order['status']=='Approved') echo 'selected'; ?>>Approved</option>
                                <option value="Completed" <?php if($order['status']=='Completed') echo 'selected'; ?>>Completed</option>
                                <option value="Rejected" <?php if($order['status']=='Rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Response Message</label>
                            <textarea name="response" rows="5" class="form-control" placeholder="Write your message..."><?php echo htmlspecialchars($order['admin_response']); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-hover py-2 fs-5">Send Response</button>
                    </form>

                    <a href="orders.php" class="d-block text-center mt-3 text-decoration-none text-secondary">
                        ⬅ Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

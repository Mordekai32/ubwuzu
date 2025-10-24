<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!');</script>";
        exit;
    }

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, role_id)
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $full_name, $email, $password, $phone, $role_id);

        if ($stmt->execute()) {
            echo "<script>alert('🎉 Registration successful!'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error: Could not register.');</script>";
        }
        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Registration | Ubwuzu System</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1E3A8A, #3B82F6);
    min-height: 100vh;
    overflow-x: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeInBody 1.5s ease-in-out;
}

@keyframes fadeInBody {
    from {opacity:0; transform:scale(1.02);}
    to {opacity:1; transform:scale(1);}
}

.glass {
    background: rgba(255,255,255,0.93);
    backdrop-filter: blur(12px);
    border:1px solid rgba(255,255,255,0.3);
    border-radius: 2rem;
    width: 100%;
    max-width: 400px; /* medium width */
    height: 640px;    /* fixed height */
    padding: 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    animation: floatCard 2s ease-in-out infinite alternate;
}

@keyframes floatCard {
    from {transform: translateY(0px);}
    to {transform: translateY(-8px);}
}

input:focus, select:focus {
    outline:none;
    box-shadow:0 0 0 3px #2563EB,0 0 10px #60A5FA;
    transition:0.3s;
}

.btn-animated {
    transition: all 0.3s ease;
    background: linear-gradient(90deg, #1E3A8A, #2563EB);
    position: relative;
    overflow: hidden;
}

.btn-animated::before {
    content:"";
    position:absolute;
    top:0; left:-100%;
    width:100%; height:100%;
    background:rgba(255,255,255,0.2);
    transform:skewX(-20deg);
    transition: all 0.4s ease;
}

.btn-animated:hover::before {left:120%;}

.icon-input {
    position: relative;
    animation: fadeInUp 0.8s ease forwards;
}

.icon-input i {
    position:absolute;
    top:50%;
    left:14px;
    transform:translateY(-50%);
    color:#2563EB;
}

.icon-input input, .icon-input select {
    padding-left:40px;
}

@keyframes fadeInUp {
    from {opacity:0; transform:translateY(20px);}
    to {opacity:1; transform:translateY(0);}
}
</style>
</head>
<body>

<div class="glass">
  <div>
    <h2 class="text-3xl text-center font-extrabold text-blue-700 mb-3">Create Account</h2>
    <p class="text-center text-gray-600 mb-4 text-sm">Join the <span class="font-semibold text-blue-700">Ubwuzu Management System</span></p>

    <form action="" method="POST" class="space-y-4">

      <div class="icon-input">
        <i class="fas fa-user"></i>
        <input type="text" name="full_name" placeholder="Full Name" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 transition">
      </div>

      <div class="icon-input">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Email Address" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 transition">
      </div>

      <div class="icon-input">
        <i class="fas fa-phone"></i>
        <input type="text" name="phone" placeholder="Phone Number" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 transition">
      </div>

      <div class="icon-input">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 transition">
      </div>

      <div class="icon-input">
        <i class="fas fa-user-tag"></i>
        <select name="role_id" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 transition">
          <option value="3">Customer</option>
        </select>
      </div>

      <button type="submit" class="btn-animated w-full py-3 text-white font-semibold rounded-lg shadow-md">Create Account</button>
    </form>
  </div>

  <p class="text-center text-gray-600 mt-4 text-sm">
    Already have an account? <a href="login.php" class="text-blue-700 font-semibold hover:underline">Login here</a>
  </p>

  <p class="text-center mt-2 text-xs text-gray-500">© 2025 Ubwuzu System | Designed by <b>Mordekai</b></p>
</div>

</body>
</html>

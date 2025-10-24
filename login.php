<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Ubwuzu System</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #1E3A8A, #3B82F6);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1E3A8A;
    }

    .card-login {
      background-color: #fff;
      border-radius: 2rem;
      padding: 3rem 2.5rem;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      transition: all 0.3s ease-in-out;
    }

    .card-login:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 12px 28px rgba(0,0,0,0.25);
    }

    .form-control {
      border-radius: 1rem;
      padding: 0.75rem 1rem;
      border: 1px solid #93C5FD;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: #1E3A8A;
      box-shadow: 0 0 0 0.25rem rgba(30,58,138,0.25);
    }

    .btn-login {
      background-color: #1E3A8A;
      color: white;
      font-weight: 600;
      border-radius: 2rem;
      padding: 0.75rem;
      transition: all 0.3s;
    }

    .btn-login:hover {
      background-color: #2563EB;
      transform: scale(1.05);
    }

    a {
      color: #1E3A8A;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    a:hover {
      color: #2563EB;
      text-decoration: underline;
    }

    .logo {
      width: 70px;
      margin-bottom: 1rem;
      border-radius:2pc;
    }

    /* Floating animation for the card */
    @keyframes floatCard {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }

    .card-float {
      animation: floatCard 2s ease-in-out infinite;
    }

  </style>
</head>
<body>

  <div class="card-login text-center animate__animated animate__fadeInDown card-float">
    <img src="a.jpeg" alt="Ubwuzu Logo" class="logo animate__animated animate__zoomIn animate__delay-1s">
    <h2 class="fw-bold mb-3 animate__animated animate__fadeInUp animate__delay-1s">Welcome Back!</h2>
    <p class="text-muted mb-4 animate__animated animate__fadeInUp animate__delay-1s">Log in to continue to <strong>Ubwuzu System</strong></p>

    <form action="" method="POST" class="d-grid gap-3 animate__animated animate__fadeInUp animate__delay-2s">
      <div class="text-start">
        <label for="email" class="form-label fw-semibold">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
      </div>

      <div class="text-start">
        <label for="password" class="form-label fw-semibold">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
      </div>

      <button type="submit" class="btn btn-login btn-lg mt-3 animate__animated animate__pulse animate__infinite">Login</button>
    </form>

    <p class="mt-4 animate__animated animate__fadeInUp animate__delay-3s">Don’t have an account? <a href="register.php">Register</a></p>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, full_name, password, role_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role_id'] = $user['role_id'];

            if ($user['role_id'] == 1) {
                header("Location: admin_dashboard.php");
            } elseif ($user['role_id'] == 2) {
                header("Location: staff_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Email not found!'); window.location='login.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

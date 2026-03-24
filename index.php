<?php
session_start();
require "database/db.php";

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Get user info
    $sql = "
        SELECT u.user_id, u.password, s.full_name, s.role, u.is_active
        FROM users u
        JOIN staff s ON u.staff_id = s.staff_id
        WHERE u.username = ?
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['is_active'] == 0) {
            $message = "Account not activated! Please activate first.";
            $messageType = "warning";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['full_name'];

            if ($user['role'] === 'admin') {
                header("Location: adminDashboard.php");
                exit;
            } 
            elseif($user['role'] === 'manager'){
            header("Location: managerDashboard.php");
                exit;
            }
            elseif($user['role'] === 'pharmacist'){
            header("Location: pharmacistDashboard.php");
                exit;
            }
            else {
                header("Location: dashboard.php");
                exit;
            }
        } else {
            $message = "Incorrect password.";
            $messageType = "danger";
        }
    } else {
        $message = "Username not found.";
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login | Dispensary Financial Monitoring System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="login_style.css" />
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

  <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h4 class="text-center text-primary mb-3">🩺 Dispensary Financial Monitoring System</h4>
    <h4 class="text-center text-primary mb-3">🔐 Login</h4>

    <!-- Show message if exists -->
    <?php if (!empty($message)): ?>
      <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <form id="loginForm" action="" method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" id="username" class="form-control" placeholder="Enter username" name="username" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" id="password" class="form-control" placeholder="Enter password" name="password" required />
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="remember" />
        <label class="form-check-label" for="remember">Remember me</label>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <a href="activateAcccount.php" class="mt-2 d-block">
      <button type="button" class="btn btn-secondary w-100">Activate Account</button>
    </a>

    <!-- 🔗 Forgot Password -->
    <div class="mt-3 text-center">
      <a href="forgotPassword.php" class="text-decoration-none">🔑 Forgot Password?</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

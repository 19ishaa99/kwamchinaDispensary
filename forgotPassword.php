<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forgot Password | Kamchina Dispensary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="login_style.css" />
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
  <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h4 class="text-center text-primary mb-3">🩺 DISPENSARY FINANCIAL TRACKER SYSTEM</h4>
    <h4 class="text-center text-primary mb-3">🔑 Forgot Password</h4>
    <form id="forgotForm" onsubmit="return validateForgot()">
      <div class="mb-3">
        <label class="form-label">Enter your registered email</label>
        <input type="email" id="forgotEmail" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
    </form>
  </div>

  <script>
    function validateForgot() {
      const email = document.getElementById("forgotEmail").value;
      alert(`📧 Reset link sent to ${email} (demo only)`);
      return false;
    }
  </script>
</body>
</html>

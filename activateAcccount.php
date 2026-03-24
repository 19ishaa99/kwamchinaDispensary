<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>activate Account | DISPENSARY FINANCIAL MONITORING SYSTEM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="login_style.css" />
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
  <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h4 class="text-center text-primary mb-3">🩺 DISPENSARY FINANCIAL MONITORING SYSTEM</h4>
    <h4 class="text-center text-primary mb-3">🔑 ACTIVATE ACCOUNT</h4>
<form method="POST" action="database/activationHandler.php">
  <input type="text" name="username" class="form-control" placeholder="Username" required>
  <br>
  <input type="password" name="old_password" class="form-control" placeholder="Default Password" required>
  <br>
  <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
  <br>
  <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
  <br>

  <button type="submit" name="activate" class="btn btn-primary">Activate Account</button>
 
</form>
<br>
 <div>
   <a href="index.php"><button type="submit" name="activate" class="btn btn-primary">Sign in</button></a>
 </div>
  </div>

  
</body>
</html>


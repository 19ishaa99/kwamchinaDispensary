<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register New User</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css">
</head>

<body>
<?php include 'database/sidebar.php'; ?>

<div class="main-content">
  <div class="container mt-5">

    <div class="card shadow-sm">
      <div class="card-header bg-info text-white">
        👤 Register New User
      </div>

      <div class="card-body">
        <form method="POST" action="database/createUserhandler.php" class="row g-3">

          <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control"
                   name="full_name"
                   placeholder="e.g. Aisha Nassor"
                   required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Staff Number</label>
            <input type="text" class="form-control"
                   name="staff_number"
                   placeholder="e.g. ST-1023"
                   required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="">-- Select Role --</option>
              <option value="admin">Admin</option>
              <option value="manager">Manager</option>
              <option value="pharmacist">Pharmacist</option>
              <option value="clerk">Accountant</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" class="form-control"
                   name="username"
                   placeholder="Login username"
                   required>
          </div>

          <!-- ACTION BUTTONS -->
          <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-success" name="create">
              ➕ Create User
            </button>
          </div>

          <div class="col-md-3 d-grid">
            <a href="view_users.php" class="btn btn-primary">
              👥 View Users
            </a>
          </div>

        </form>
      </div>
    </div>

  </div>
</div>

<?php if (isset($_GET['success'])): ?>
<script>
  alert("✅ User created successfully!");
</script>
<?php endif; ?>

</body>
</html>

<?php
require "database/db.php";

/* 🔹 Fetch all users with staff details */
$stmt = $pdo->query("
    SELECT 
        u.user_id,
        u.username,
        s.full_name,
        s.role,
        s.is_active
    FROM users u
    JOIN staff s ON u.staff_id = s.staff_id
    ORDER BY s.full_name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
  <?php include 'database/sidebar.php'; ?>

<div class="main-content">
  <div class="container mt-5">
    <h3 class="mb-4 text-primary">👥 Registered Users</h3>

    <!-- Search Bar -->
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Search users..." id="searchInput" />
      <button class="btn btn-outline-secondary">🔍</button>
    </div>

    <!-- Users Table -->
    <table class="table table-bordered table-hover" id="usersTable">
      <thead class="table-info">
        <tr>
          <th>#</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Role</th>
          <th>Activate / Deactivate</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= ucfirst($row['role']) ?></td>
          <td>
            <?php if ($row['is_active']): ?>
              <a href="database/toggle_user.php?id=<?= $row['user_id'] ?>&action=deactivate"
                 class="btn btn-warning btn-sm w-100"
                 onclick="return confirm('Deactivate this user?')">
                ⛔ Deactivate
              </a>
            <?php else: ?>
              <a href="database/toggle_user.php?id=<?= $row['user_id'] ?>&action=activate"
                 class="btn btn-primary btn-sm w-100"
                 onclick="return confirm('Activate this user?')">
                ✅ Activate
              </a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

  <!-- Simple client-side search -->
  <script>
    document.getElementById("searchInput").addEventListener("keyup", function () {
      const filter = this.value.toLowerCase();
      document.querySelectorAll("#usersTable tbody tr").forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter)
          ? ""
          : "none";
      });
    });
  </script>
  </div>
</div>
</body>
</html>

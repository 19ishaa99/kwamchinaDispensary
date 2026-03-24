<?php
//session_start();
require "database/db.php";

/*
 Optional access control
 Uncomment if you want only logged-in users
*/
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

// Fetch lab tests
try {
    $stmt = $pdo->query("
        SELECT 
            lab_test_id,
            name,
            description,
            cost
        FROM lab_tests
        ORDER BY name ASC
    ");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lab Tests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'database/sidebar.php'; ?>

<div class="main-content">
  <div class="container mt-5">

    <h3 class="mb-4 text-primary">🧪 Lab Tests</h3>

    <!-- Alerts -->
    <?php if (isset($_GET['added'])): ?>
      <div class="alert alert-success">Lab test added successfully</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-danger">Lab test deleted successfully</div>
    <?php endif; ?>

    <!-- Search -->
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Search lab test..." id="searchInput">
      <button class="btn btn-outline-secondary" type="button">🔍</button>
    </div>

    <!-- Table -->
    <table class="table table-bordered table-hover" id="labTestTable">
      <thead class="table-info">
        <tr>
          <th>#</th>
          <th>Test Name</th>
          <th>Description</th>
          <th>Cost (TZS)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
        ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['description'] ?: '-') ?></td>
          <td><?= number_format($row['cost'], 2) ?></td>
          <td>
            <a href="edit_lab_test.php?id=<?= $row['lab_test_id'] ?>"
               class="btn btn-success btn-sm">
               ✏️ Edit
            </a>
          </td>
        </tr>
        <?php endwhile; ?>

        <?php if ($i === 1): ?>
        <tr>
          <td colspan="5" class="text-center text-muted">
            No lab tests found
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>

  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Search Script -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
  const filter = this.value.toLowerCase();
  document.querySelectorAll("#labTestTable tbody tr").forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter)
      ? ""
      : "none";
  });
});
</script>

</body>
</html>

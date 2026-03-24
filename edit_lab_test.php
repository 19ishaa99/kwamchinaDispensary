<?php
//session_start();
require "database/db.php";

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$lab_test_id = (int) $_GET['id'];

// Fetch existing lab test
$stmt = $pdo->prepare("SELECT * FROM lab_tests WHERE lab_test_id = ?");
$stmt->execute([$lab_test_id]);
$test = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$test) {
    die("Lab test not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Lab Test</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'database/sidebar.php'; ?>
<div class="main-content">

<div class="container mt-5">
  <h4 class="mb-4 text-primary">✏️ Edit Lab Test</h4>

  <form method="POST" action="database/update_labTest.php">
    <input type="hidden" name="lab_test_id" value="<?= $test['lab_test_id'] ?>">

    <div class="mb-3">
      <label class="form-label">Test Name</label>
      <input type="text" name="name" class="form-control"
             value="<?= htmlspecialchars($test['name']) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control"
                rows="3"><?= htmlspecialchars($test['description']) ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Cost (TZS)</label>
      <input type="number" name="cost" class="form-control"
             step="0.01" value="<?= $test['cost'] ?>" required>
    </div>

    <button type="submit" class="btn btn-success">💾 Update</button>
  </form>
</div>
</div>
</body>
</html>

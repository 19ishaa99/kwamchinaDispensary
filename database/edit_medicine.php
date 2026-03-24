<?php
require "db.php";

if (!isset($_GET['id'])) {
    header("Location: view_medicine.php");
    exit;
}

$medicine_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM medicines WHERE medicine_id = ?");
$stmt->execute([$medicine_id]);
$medicine = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicine) {
    die("Medicine not found");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Medicine</title>
  <link href="../bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container mt-5">
  <div class="card">
    <div class="card-header bg-info text-white">✏️ Edit Medicine</div>
    <div class="card-body">

      <!-- ✅ THIS FORM POSTS TO update_medicine.php -->
      <form method="POST" action="update_medicine.php">

        <!-- VERY IMPORTANT -->
        <input type="hidden" name="medicine_id" class="form-control"
               value="<?= $medicine['medicine_id'] ?>">

        <div class="mb-3">
          <label>Medicine Name</label>
          <input type="text" name="name"
                 class="form-control"
                 value="<?= htmlspecialchars($medicine['name']) ?>" required>
        </div>

        <div class="mb-3">
          <label>Type</label>
          <input type="text" name="type"
                 class="form-control"
                 value="<?= htmlspecialchars($medicine['type']) ?>">
        </div>

        <div class="mb-3">
          <label>Unit Price</label>
          <input type="number" step="0.01" name="unit_price"
                 class="form-control"
                 value="<?= $medicine['unit_price'] ?>" required>
        </div>

        <div class="mb-3">
          <label>Expiry Date</label>
          <input type="date" name="expiry_date"
                 class="form-control"
                 value="<?= $medicine['expiry_date'] ?>">
        </div>

        <button class="btn btn-success w-100">Update Medicine</button>
      </form>

    </div>
  </div>
</div>
</body>
</html>

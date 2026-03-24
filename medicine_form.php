<?php
//session_start();
require "database/db.php";

/* 🔹 Fetch medicines for dropdown */
$stmt = $pdo->query("SELECT medicine_id, name FROM medicines ORDER BY name ASC");
$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Morning Stock Update</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'database/sidebar.php'; ?>
<div class="main-content">
    <!-- Page content goes here -->
     <div class="container mt-5">
    <div class="card shadow-sm">
      <div class="card-header bg-info text-white">💊 Morning Stock Update</div>
      <div class="card-body">
        <form id="medicineForm" action="database/stock.php" method="POST" class="row g-3 align-items-end">

         <div class="col-md-4">
    <label class="form-label">Medicine Name</label>
    <select name="medicine_id" class="form-select" required>
      <option value="">-- Select Medicine --</option>
      <?php foreach ($medicines as $medicine): ?>
        <option value="<?= $medicine['medicine_id']; ?>">
          <?= htmlspecialchars($medicine['name']); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Quantity</label>
    <input class="form-control" name="quantity" type="number" min="1" required>
  </div>

  <div class="col-md-2 d-grid">
    <button type="submit" class="btn btn-success">➕ Add Stock</button>
  </div>

  <div class="col-md-3 d-grid">
    <a href="view_medicine.php" class="btn btn-primary">🏥 View Medicines</a>
  </div>


          <!-- Add New Medicine Type Button -->
          <div class="col-md-3 d-grid">
            <a href="add_new_medicine.php" class="btn btn-outline-primary">➕ Add New Medicine Type</a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
  
</body>
</html>

<?php
require "database/db.php";

// Fetch all medicines with their stock
$stmt = $pdo->query("
    SELECT 
        m.medicine_id,
        m.name,
        m.type,
        m.unit_price,
        ms.quantity
    FROM medicines m
    JOIN medicine_stock ms ON m.medicine_id = ms.medicine_id
    ORDER BY m.name ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Lab Test</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
  <?php include 'database/sidebar.php'; ?>
<div class="main-content">
    <!-- Page content goes here -->
     <div class="container mt-5">
    <h3 class="mb-4 text-primary">🏥 lab Tests</h3>

    <?php if (isset($_GET['deleted'])): ?>
    <script>
    alert ("medicine deleted successfully");
  </script>
<?php endif; ?>


    <!-- Search Bar -->
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Search medicine..." id="searchInput" />
      <button class="btn btn-outline-secondary">🔍</button>
    </div>

    <!-- Medicine Table -->
    <table class="table table-bordered table-hover" id="medicineTable">
      <thead class="table-info">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Added By</th>
          <th>Price</th>
          <th colspan="2">Action</th>
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
          <td><?= htmlspecialchars($row['type']) ?></td>
          <td><?= $row['quantity'] ?></td>
          <td><?= number_format($row['unit_price'], 2) ?></td>

          <!-- EDIT BUTTON -->
          <td>
            <a href="database/view_Test.php?id=<?= $row['medicine_id'] ?>"
               class="btn btn-success btn-sm w-100">
               ✏️ Edit
            </a>
          </td>

          <!-- DELETE BUTTON (later) -->
          <td>

          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    

  <!-- Simple client-side search -->
  <script>
    document.getElementById("searchInput").addEventListener("keyup", function () {
      const filter = this.value.toLowerCase();
      document.querySelectorAll("#medicineTable tbody tr").forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter)
          ? ""
          : "none";
      });
    });
  </script>
</div>

  
</body>
</html>

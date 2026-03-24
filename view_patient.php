<?php
require "database/db.php";

// Fetch all patients with their latest visit/payment info
$stmt = $pdo->query("
    SELECT 
        p.patient_id,
        p.full_name,
        p.gender,
        p.phone,
        pv.visit_date,
        pv.total_amount,
        pv.is_paid
    FROM patients p
    LEFT JOIN patient_visits pv ON p.patient_id = pv.patient_id
    ORDER BY pv.visit_date DESC, p.full_name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Patients</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
<?php include 'database/sidebar.php'; ?>
<div class="main-content">
  <div class="container mt-5">
    <h3 class="mb-4 text-primary">🏥 Patients</h3>

    <!-- Search Bar -->
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Search patient..." id="searchInput" />
      <button class="btn btn-outline-secondary">🔍</button>
    </div>

    <!-- Patients Table -->
    <table class="table table-bordered table-hover" id="patientsTable">
      <thead class="table-info">
        <tr>
          <th>#</th>
          <th>Full Name</th>
          <th>Gender</th>
          <th>Phone</th>
          <th>Last Visit</th>
          <th>Total Amount</th>
          <th>Paid?</th>
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
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= htmlspecialchars($row['gender']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= $row['visit_date'] ?? '-' ?></td>
          <td><?= isset($row['total_amount']) ? number_format($row['total_amount'], 2) : '-' ?></td>
          <td>
            <?php 
              if (!isset($row['is_paid'])) {
                  echo '-';
              } else {
                  echo $row['is_paid'] ? '✅ Paid' : '❌ Unpaid';
              }
            ?>
          </td>
          <td>
            <a href="database/edit_patient.php?patient_id=<?= $row['patient_id'] ?>" class="btn btn-sm btn-warning"> ✏️ Edit</a>

          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <!-- Simple client-side search -->
    <script>
      document.getElementById("searchInput").addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#patientsTable tbody tr").forEach(row => {
          row.style.display = row.textContent.toLowerCase().includes(filter)
            ? ""
            : "none";
        });
      });
    </script>
  </div>
</div>
<?php if (!empty($message)): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
  <?= htmlspecialchars($message) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

</body>
</html>

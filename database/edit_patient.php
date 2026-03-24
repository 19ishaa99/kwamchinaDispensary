<?php
require "db.php";

// Initialize message variables
$message = "";
$messageType = "";

// Check if patient_id is provided
if (!isset($_GET['patient_id'])) {
    die("Patient ID not provided.");
}

$patient_id = intval($_GET['patient_id']);

// Fetch patient details
$stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    die("Patient not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $full_name = $_POST['full_name'];
        $gender    = $_POST['gender'];
        $phone     = $_POST['phone'];

        $update = $pdo->prepare("UPDATE patients SET full_name = ?, gender = ?, phone = ? WHERE patient_id = ?");
        $update->execute([$full_name, $gender, $phone, $patient_id]);

        $message = "Patient updated successfully!";
        $messageType = "success";

        
    } catch (Exception $e) {
        $message = "Update failed: " . $e->getMessage();
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Patient</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="container mt-5">

  <h2 class="mb-4">Edit Patient</h2>

  <!-- Success/Failure Alert -->
  <?php if (!empty($message)): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Edit Form -->
  <form method="POST" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($patient['full_name']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Gender</label>
      <select name="gender" class="form-select" required>
        <option value="Male" <?= $patient['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $patient['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($patient['phone']) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Patient</button>
    <a href="view_patient.php" class="btn btn-secondary">Back</a>
  </form>

</body>
</html>

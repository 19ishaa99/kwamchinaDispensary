
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>new medicine</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap.min.css">
</head>
<body>
  <?php include 'database/sidebar.php'; ?>
<div class="main-content">
    <!-- Your page content here -->
       <div class="container mt-5">
        <div class="card">
          <div class="card-header bg-info text-white">💊 new Medicine</div>
          <div class="card-body">
    <!-- new Medicine Form -->
<section id="medicine" class="container mt-5">
    
    <form id="medicineForm" class="row g-3" action="database/save_medicine.php" method="POST" onsubmit="validateMedicineForm()">
  <div class="col-md-4">
        <input class= "form-control" type="text" id="med_name" name="med_name" placeholder="medicine name" required>
</div>
<div class="col-md-4">
  <select class="form-select" id="med_type" name="med_type" required>
    <option value="">-- Select Medicine Type --</option>
    <option value="Tablet">Tablet</option>
    <option value="Capsule">Capsule</option>
    <option value="Syrup">Syrup</option>
    <option value="Injection">Injection</option>
  </select>
</div>
<div class="col-md-4">
  <input class="form-control" type="number" id="quantity" name="quantity" min="1" placeholder="quantity" required>
</div>
  <div class="col-md-4">
  <input class="form-control" type="date" id="expiry_date" name="expiry_date" placeholder="expiry date" required>
</div>
<div class="col-md-4">
  <input class="form-control" type="number" id="price" name="price" min="0" placeholder="price " required>
</div>
  <button type="submit" class="btn btn-primary w-100" name="add">Add Medicine</button>
</form>
</section>
</div>
</div>
</div>
</div>

      
</body>
</html>

<script>
function validateMedicineForm() {
  const medName = document.getElementById("med_name").value.trim();
  const quantity = parseInt(document.getElementById("quantity").value);
  const price = parseFloat(document.getElementById("price").value);
  const expiryDate = new Date(document.getElementById("expiry_date").value);
  const today = new Date();
  today.setHours(0,0,0,0);

  if (medName === "") {
    alert("Medicine name is required.");
    return false;
  }

  if (quantity <= 0) {
    alert("Quantity must be greater than zero.");
    return false;
  }

  if (price < 0) {
    alert("Price cannot be negative.");
    return false;
  }

  if (expiryDate <= today) {
    alert("Expiry date must be in the future.");
    return false;
  }

  return true;
}
</script>


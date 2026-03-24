<?php
require "database/db.php";

// Fetch medicines
$medicinesStmt = $pdo->query("SELECT medicine_id, name, unit_price FROM medicines ORDER BY name ASC");
$medicines = $medicinesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch lab tests
$labTestsStmt = $pdo->query("SELECT lab_test_id, name, cost FROM lab_tests ORDER BY name ASC");
$labTests = $labTestsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kamchina Dispensary - Patient Invoice</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <?php include 'database/sidebar.php'; ?>
<div class="main-content">
    <!-- Page content goes here -->
      <div class="container py-5">

    <!-- 👤 Patient Info -->
    <div class="card shadow-sm mb-5">
      <div class="card-header bg-info text-white">👤 Patient Information</div>
      <div class="card-body">
        <form id="patientForm" class="row g-3" action="database/save_patient.php" method="POST">
          <div class="col-md-6">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" required />
          </div>
          <div class="col-md-3">
            <label for="weight" class="form-label">Age</label>
            <input type="number" name="age" id="weight" class="form-control" min="1" step="0.1" />
          </div>
          <div class="col-md-3">
            <label for="gender" class="form-label">Gender/Sex</label>
            <select name="gender" id="gender" class="form-select" required>
              <option value="">Choose...</option>
              <option value="Female">Female</option>
              <option value="Male">Male</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" name="phone" id="phone" class="form-control" required />
          </div>
          <div class="col-md-6">
            <label for="address" class="form-label">Address</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="e.g. Shangani, Zanzibar" />
          </div>
          <div class="col-md-3">
            <label for="weight" class="form-label">Weight (kg)</label>
            <input type="number" name="weight" id="weight" class="form-control" min="1" step="0.1" />
          </div>
        <div class="form-check mb-3">
  <input class="form-check-input" type="checkbox" name="consultation" id="consultation" value="yes" />
  <label class="form-check-label" for="consultation">Doctor Consultance?</label>
</div>

          <div class="col-md-12">
            <label for="notes" class="form-label">Diagnosis / Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
          </div>

          <!-- 💊 Prescribed Medicines -->
           <div><h3>PATIENT MEDICINES</h3></div>
          <div id="medicineContainer">
            <div class="row g-3 medicine-group">
              <div class="col-md-3">
                <label class="form-label">Medicine Name</label>
                <select name="medicine_id[]" class="form-select" required>
    <option value="">-- Select Medicine --</option>
    <?php foreach($medicines as $med): ?>
        <option value="<?= $med['medicine_id'] ?>" data-price="<?= $med['unit_price'] ?>">
            <?= htmlspecialchars($med['name']) ?> — TSh <?= number_format($med['unit_price'],2) ?>
        </option>
    <?php endforeach; ?>
</select>

              </div>
              <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity[]" class="form-control" min="1" required />
              </div>
              <div class="col-md-3">
                <label class="form-label">Unit Price (TSh)</label>
                <input type="number" name="unit_price[]" class="form-control" min="0" required />
              </div>
              <div class="col-md-3">
                <label class="form-label">Instructions</label>
                <input type="text" name="instructions[]" class="form-control" placeholder="e.g. 1 tablet every 8 hrs" />
              </div>
            </div>
          </div>
          <br>

           <div class="text-end mt-3">
            <button type="button" class="btn btn-secondary" onclick="addMedicine()">➕ Add Another Medicine</button>
          </div>
<!-- 💊 Prescribed Lab test -->
          <div><h3>LAB TEST</h3></div>
           <div id="labTestsContainer">
            <div class="row g-3 lab-test-group">
              <div class="col-md-4">
                <label class="form-label">Test Name</label>
                <select name="lab_test_id[]" class="form-select" required>
    <option value="">-- Select Test --</option>
    <?php foreach($labTests as $test): ?>
        <option value="<?= $test['lab_test_id'] ?>" data-cost="<?= $test['cost'] ?>">
            <?= htmlspecialchars($test['name']) ?> — TSh <?= number_format($test['cost'],2) ?>
        </option>
    <?php endforeach; ?>
</select>

              </div>
              <div class="col-md-4">
                <label class="form-label">Result</label>
                <input type="text" name="result[]" class="form-control" required />
              </div>
              <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" name="test_date[]" class="form-control" required />
              </div>
            </div>
          </div>
          <br>
           <div class="col-md-1">
            <button type="submit" class="btn btn-success w-100">save</button>
          </div>
          <div class="text-end mt-3">
            <button type="button" class="btn btn-secondary" onclick="addLabTest()">➕ Add Another Test</button>
          </div>
        </form>
      </div>
    </div>

    

    <!-- 🧮 Invoice Trigger -->
    <div class="text-end mt-4">
      <button type="button" class="btn btn-warning" onclick="calculateTotal()">🧮 Calculate Total Cost</button>
    </div>
    <div class="mt-3">
      <h5>Total Cost: <span id="totalCost" class="text-success">TSh 0</span></h5>
    </div>

    <!-- 🧾 Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content" id="invoiceContent">
          <div class="modal-header">
            <h5 class="modal-title" id="invoiceLabel">Patient Invoice</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p><strong>Patient Name:</strong> <span id="invoicePatientName">John Doe</span></p>
            <h6>Medicines</h6>
            <ul id="invoiceMedicines"></ul>
            <h6>Lab Tests</h6>
            <ul id="invoiceLabTests"></ul>
            <h5 class="mt-3">Total: <span id="invoiceTotal" class="text-success">TSh 0</span></h5>
          </div>

          <div class="modal-footer">
            <button class="btn btn-success" onclick="markAsPaid()">✅ Mark as Paid</button>
            <button class="btn btn-secondary" onclick="printInvoice()">🖨️ Print Invoice</button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- 🔧 Scripts -->
  <script>
    function addMedicine() {
      const container = document.getElementById('medicineContainer');
      const newGroup = container.querySelector('.medicine-group').cloneNode(true);
      newGroup.querySelectorAll('input, select').forEach(el => el.value = '');
      container.appendChild(newGroup);
    }

    function addLabTest() {
      const container = document.getElementById('labTestsContainer');
      const newGroup = container.querySelector('.lab-test-group').cloneNode(true);
      newGroup.querySelectorAll('input, select').forEach(el => el.value = '');
      container.appendChild(newGroup);
    }

    function calculateTotal() {
    let total = 0;

    // Medicines
    document.querySelectorAll('select[name="medicine_id[]"]').forEach((select, index) => {
        const qty = parseFloat(document.querySelectorAll('input[name="quantity[]"]')[index].value) || 0;
        const price = parseFloat(select.selectedOptions[0].dataset.price) || 0;
        total += qty * price;
    });

    // Lab tests
    document.querySelectorAll('select[name="lab_test_id[]"]').forEach(select => {
        const cost = parseFloat(select.selectedOptions[0].dataset.cost) || 0;
        total += cost;
    });

    document.getElementById('totalCost').textContent = `TSh ${total.toLocaleString()}`;
}


      // Doctor Consultance Fee
const consultCheckbox = document.getElementById("consultation");
if (consultCheckbox.checked) {
  const consultFee = 10000; // You can change this value
  total += consultFee;

  const li = document.createElement('li');
  li.textContent = `Doctor Consultance — TSh ${consultFee}`;
  document.getElementById('invoiceLabTests').appendChild(li); // or create a separate section
}


      const medicineGroups = document.querySelectorAll('.medicine-group');
            medicineGroups.forEach(group => {
        const name = group.querySelector('select[name="medicine_name[]"]').value;
        const qty = parseFloat(group.querySelector('input[name="quantity[]"]').value) || 0;
        const price = parseFloat(group.querySelector('input[name="unit_price[]"]').value) || 0;
        const cost = qty * price;
        total += cost;

        const li = document.createElement('li');
        li.textContent = `${name} — ${qty} × TSh ${price} = TSh ${cost}`;
        medicineList.appendChild(li);
      });

      const labTests = document.querySelectorAll('.lab-test-group');
      labTests.forEach(test => {
        const testName = test.querySelector('select[name="test_name[]"]').value;
        const testCost = 5000; // You can make this dynamic later
        total += testCost;

        const li = document.createElement('li');
        li.textContent = `${testName} — TSh ${testCost}`;
        labTestList.appendChild(li);
      });

      document.getElementById('invoiceTotal').textContent = `TSh ${total.toLocaleString()}`;
      document.getElementById('totalCost').textContent = `TSh ${total.toLocaleString()}`;

      const invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
      invoiceModal.show();
    

    function printInvoice() {
      const printContents = document.getElementById('invoiceContent').innerHTML;
      const originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
      location.reload();
    }

    function markAsPaid() {
      alert("✅ Payment recorded!");
      // You can send this to the backend later
    }
  </script>

  <!-- ✅ Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>

 
</body>
</html>

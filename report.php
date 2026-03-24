<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Generate Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    #reportPreview { display: none; }
    @media print {
      body * {
        visibility: hidden;
      }
      #reportPreview, #reportPreview * {
        visibility: visible;
      }
      #reportPreview {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
      }
    }
  </style>
</head>
<body>
   <?php include 'database/sidebar.php'; ?>
<div class="main-content">
  <section id="reports" class="container mt-5 mb-5">
    <h4 class="mb-4 text-primary">📄 Generate Report</h4>
    <form id="reportForm" class="row g-3" action="database/fetch_report.php" method="POST">
      <div class="col-md-4">
        <select class="form-select" name="report_type" required>
          <option value="">Select Report Type</option>
          <option value="income_expense">Monthly Income/Expense</option>
          <option value="patient_records">Patient Records</option>
        </select>
      </div>
      <div class="col-md-3">
        <input type="date" name="start_date" class="form-control" required />
      </div>
      <div class="col-md-3">
        <input type="date" name="end_date" class="form-control" required />
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-dark w-100">🖨️ Generate</button>
      </div>
    </form>

    <!-- Report Preview -->
    <div id="reportPreview" class="mt-5">
      <h5 class="text-secondary">Report Preview:</h5>
      <div class="border p-3 bg-light" id="reportContent"></div>
      <button id="printBtn" class="btn btn-outline-primary mt-3">🖨️ Print Report</button>
    </div>
  </section>

  <script>
document.getElementById("reportForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const preview = document.getElementById("reportPreview");
    const content = document.getElementById("reportContent");

    fetch("database/fetch_report.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(html => {
        content.innerHTML = html;
        preview.style.display = "block";
    })
    .catch(() => {
        content.innerHTML = "<p class='text-danger'>Failed to load report</p>";
        preview.style.display = "block";
    });
});

document.getElementById("printBtn").addEventListener("click", function() {
    window.print();
});
</script>

  </div>
</body>
</html>

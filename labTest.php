
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>lab test </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="main-content">
    <?php require "database/sidebar.php";?>
    <!-- Page content goes here -->
     <div class="container mt-5">
    <div class="card shadow-sm">
      <div class="card-header bg-info text-white">ADD LAB TEST</div>
      <div class="card-body">
       <form action="database/save_lab_test.php" method="POST" class="row g-3">

  <div class="col-md-4">
    <label class="form-label">Lab Test</label>
    <input type="text" class="form-control" name="labTest" required placeholder="lab test">
  </div>

  <div class="col-md-5">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" placeholder="description"></textarea>
  </div>

  <div class="col-md-3">
    <label class="form-label">Cost</label>
    <input type="number" step="0.01" min="0" class="form-control" name="cost" required placeholder="cost">
  </div>

  <div class="col-md-2 d-grid">
    <button type="submit" class="btn btn-success">➕ Add Lab Test</button>
  </div>

</form>

      </div>
    </div>
  </div>
</div>
  
</body>
</html>

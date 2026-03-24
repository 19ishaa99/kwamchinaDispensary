<?php
// sidebar.php
session_start();
$user_name = $_SESSION['name'] ?? 'Guest';
$role = $_SESSION['role'] ?? '';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
/* Sidebar styling */
.sidebar {
    height: 100vh;
    width: 220px;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #0dcaf0;
    padding-top: 60px;
    color: white;
    overflow-y: auto;
}
.sidebar a {
    display: block;
    padding: 12px 20px;
    color: black;
    text-decoration: none;
    font-weight: 500;
}
.sidebar a:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}
.sidebar .active {
    background-color: rgba(255, 255, 255, 0.4);
    border-radius: 4px;
}
.main-content {
    margin-left: 220px;
    padding: 20px;
}
.sidebar-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 220px;
    height: 60px;
    background-color: #0b5ed7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
}
</style>

<div class="sidebar-header">
    🩺 DISPENSARY FINANCIAL MONITORING SYSTEM
</div>

<div class="sidebar">
        <p style="text-align: center; color:black;"> FINANCIAL MONITORING SYSTEM </p>
    <!--<p class="text-center mt-2">Hello,<?= htmlspecialchars($user_name) ?></p>-->
   <!-- Manager only -->
       <?php if ($role === 'manager'): ?>
        <a href="managerDashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'managerDashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="view_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_users.php' ? 'active' : '' ?>">Users</a>
        <a href="view_medicine.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_medicine.php' ? 'active' : '' ?>">Medicines</a>
        <a href="view_labTest.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_labTest.php' ? 'active' : '' ?>">Lab Test</a>
        <a href="view_payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_payments.php' ? 'active' : '' ?>">Payment</a>
        <a href="expenses.php" class="<?= basename($_SERVER['PHP_SELF']) == 'expenses.php' ? 'active' : '' ?>">Expenses</a>
        <a href="report.php" class="<?= basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : '' ?>">Reports</a>
    <?php endif; 
    ?>

    <!-- Acountant only -->
          <?php if ($role === 'clerk'): ?>
            <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="view_medicine_only.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_medicine_only.php' ? 'active' : '' ?>">View Medicines</a>
        <a href="view_labTest.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_labTest.php' ? 'active' : '' ?>">View Lab Test</a>
        <a href="view_payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_payments.php' ? 'active' : '' ?>">View Payment</a>
         <a href="expenses.php" class="<?= basename($_SERVER['PHP_SELF']) == 'expenses.php' ? 'active' : '' ?>">Expenses</a>
    <a href="payment_form.php" class="<?= basename($_SERVER['PHP_SELF']) == 'payment_form.php' ? 'active' : '' ?>">Record Payment</a>
    <?php endif; 
    ?>
     <!-- Pharmacist only -->
       <?php if ($role === 'pharmacist'): ?>
        <a href='pharmacistDashboard.php' class="<?= basename($_SERVER['PHP_SELF']) == 'pharmacistDashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="view_labTest.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_labTest.php' ? 'active' : '' ?>">Lab Test</a>
         <a href="labTest.php" class="<?= basename($_SERVER['PHP_SELF']) == 'labTest.php' ? 'active' : '' ?>">Add Lab Test</a>
         <a href="view_medicine.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_medicine.php' ? 'active' : '' ?>">Medicines</a>
        <a href="add_new_medicine.php" class="<?= basename($_SERVER['PHP_SELF']) == 'add_new_medicine.php' ? 'active' : '' ?>">Add New Medicine</a>
        <a href="medicine_form.php" class="<?= basename($_SERVER['PHP_SELF']) == 'medicine_form.php' ? 'active' : '' ?>">update Medicine</a>
    <?php endif; 
    ?>


    <!-- Admin only -->
    <?php if ($role === 'admin'): ?>
        <a href="adminDashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'adminDashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="view_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_users.php' ? 'active' : '' ?>">View Users</a>
        <a href="register.php" class="<?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : '' ?>">Create Users</a>
        <a href="view_medicine.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_medicine.php' ? 'active' : '' ?>">Medicines</a>
        <a href="view_labTest.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_labTest.php' ? 'active' : '' ?>">Lab Test</a>
        <a href="view_payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_payments.php' ? 'active' : '' ?>">Payments</a>
        <a href="expenses.php" class="<?= basename($_SERVER['PHP_SELF']) == 'expenses.php' ? 'active' : '' ?>">Expenses</a>
        <a href="report.php" class="<?= basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : '' ?>">Reports</a>
        <a href="view_patient.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_patient.php' ? 'active' : '' ?>">Patient</a>
        
    <?php endif; ?>

    
    <!-- Dashboard -->
   <!-- <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>-->

    <!-- Medicines -->
    <!--<a href="view_medicine.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_medicine.php' ? 'active' : '' ?>">View Medicines</a>
    
    <a href="medicine_form.php" class="<?= basename($_SERVER['PHP_SELF']) == 'medicine_form.php' ? 'active' : '' ?>">Update Stock</a>-->

    <!-- Lab Tests -->
    <!--<a href="labTest.php" class="<?= basename($_SERVER['PHP_SELF']) == 'labTest.php' ? 'active' : '' ?>">Add Lab Test</a>-->

    <!-- Payments -->
   <!-- <a href="expenses.php" class="<?= basename($_SERVER['PHP_SELF']) == 'expenses.php' ? 'active' : '' ?>">Expenses</a>
    <a href="payment_form.php" class="<?= basename($_SERVER['PHP_SELF']) == 'payment_form.php' ? 'active' : '' ?>">Record Payment</a>
    <a href="view_payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'view_payments.php' ? 'active' : '' ?>">View Payment</a>-->

    <!-- Logout -->
    <a href="index.php">Logout</a>
</div>

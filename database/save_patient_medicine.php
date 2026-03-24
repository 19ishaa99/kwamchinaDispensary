<?php
session_start();
require "db.php";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    // Patient info
    $full_name = trim($_POST['full_name'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $notes     = trim($_POST['notes'] ?? '');
    $consult   = isset($_POST['consultation']) ? 1 : 0;

    if ($full_name === '' || $gender === '') {
        throw new Exception("Patient name and gender are required");
    }

    // Check if patient exists
    $stmt = $pdo->prepare("SELECT patient_id FROM patients WHERE full_name = ? AND phone = ? LIMIT 1");
    $stmt->execute([$full_name, $phone]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        $patient_id = $patient['patient_id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO patients (full_name, gender, phone) VALUES (?, ?, ?)");
        $stmt->execute([$full_name, $gender, $phone]);
        $patient_id = $pdo->lastInsertId();
    }

    // Create visit
    $stmt = $pdo->prepare("INSERT INTO patient_visits (patient_id, visit_date, notes, consultation) VALUES (?, NOW(), ?, ?)");
    $stmt->execute([$patient_id, $notes, $consult]);
    $visit_id = $pdo->lastInsertId();
    $_SESSION['visit_id'] = $visit_id;

    // Save medicines
    $medicines = $_POST['medicine_name'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $prices = $_POST['unit_price'] ?? [];

    if (!empty($medicines)) {
        $stmt = $pdo->prepare("INSERT INTO patient_medicines (visit_id, medicine_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($medicines as $i => $med_id) {
            $qty = intval($quantities[$i] ?? 0);
            $price = floatval($prices[$i] ?? 0);
            if ($med_id && $qty > 0 && $price > 0) {
                $stmt->execute([$visit_id, $med_id, $qty, $price]);
            }
        }
    }

    // Save lab tests
    $tests = $_POST['test_name'] ?? [];
    $results = $_POST['result'] ?? [];
    $dates = $_POST['test_date'] ?? [];
    $user_id = $_SESSION['user_id'] ?? null;

    if (!empty($tests)) {
        $stmt = $pdo->prepare("INSERT INTO patient_lab_tests (visit_id, lab_test_id, result, test_date, added_by) VALUES (?, ?, ?, ?, ?)");
        foreach ($tests as $i => $test_id) {
            $result = trim($results[$i] ?? '');
            $date = $dates[$i] ?? date('Y-m-d');
            if ($test_id && $result) {
                $stmt->execute([$visit_id, $test_id, $result, $date, $user_id]);
            }
        }
    }

    // Log activity
    $log = $pdo->prepare("INSERT INTO activity_logs (activity_type, description) VALUES ('patient', ?)");
    $log->execute(["New patient visit: $full_name"]);

    echo "<script>alert('✅ Patient, medicines, and lab tests saved successfully'); window.location.href='patient_registration_form.php';</script>";
    exit;

} catch (Exception $e) {
    echo "<script>alert('❌ Error: {$e->getMessage()}'); window.history.back();</script>";
}

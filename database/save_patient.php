<?php
session_start();
require "db.php";

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request");
    }

    $user_id = $_SESSION['user_id'];

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    /* ================= PATIENT DATA ================= */
    $full_name = trim($_POST['full_name'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $age       = intval($_POST['age'] ?? 0);
    $weight    = floatval($_POST['weight'] ?? 0);
    $address   = trim($_POST['address'] ?? '');
    $notes     = trim($_POST['notes'] ?? '');
    $consult   = isset($_POST['consultation']) ? 1 : 0;

    if ($full_name === '' || $gender === '') {
        throw new Exception("Patient name and gender required");
    }

    /* ================= CHECK PATIENT ================= */
    $stmt = $pdo->prepare(
        "SELECT patient_id FROM patients WHERE full_name = ? AND phone = ? LIMIT 1"
    );
    $stmt->execute([$full_name, $phone]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        $patient_id = $patient['patient_id'];
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO patients (full_name, gender, phone, age, weight, address)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$full_name, $gender, $phone, $age, $weight, $address]);
        $patient_id = $pdo->lastInsertId();
    }

    /* ================= CREATE VISIT ================= */
    $stmt = $pdo->prepare("
        INSERT INTO patient_visits (patient_id, visit_date, notes, total_amount, is_paid, consultation)
        VALUES (?, NOW(), ?, 0, 0, ?)
    ");
    $stmt->execute([$patient_id, $notes, $consult]);

    $visit_id = $pdo->lastInsertId();
    $_SESSION['visit_id'] = $visit_id;

    $grandTotal = ($consult ? 10000 : 0);

    /* ================= MEDICINES ================= */
    $medicines  = $_POST['medicine_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $prices     = $_POST['unit_price'] ?? [];

    if ($medicines) {
        $stmt = $pdo->prepare("
            INSERT INTO patient_medicines (visit_id, medicine_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($medicines as $i => $med_id) {
            $qty   = intval($quantities[$i] ?? 0);
            $price = floatval($prices[$i] ?? 0);

            if ($med_id && $qty > 0 && $price > 0) {
                $stmt->execute([$visit_id, $med_id, $qty, $price]);
                $grandTotal += ($qty * $price);
            }
        }
    }

    /* ================= LAB TESTS ================= */
    $tests   = $_POST['lab_test_id'] ?? [];
    $results = $_POST['result'] ?? [];
    $dates   = $_POST['test_date'] ?? [];

    if ($tests) {
        $stmt = $pdo->prepare("
            INSERT INTO patient_lab_tests (visit_id, lab_test_id, result, test_date, added_by)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($tests as $i => $test_id) {
            $result = trim($results[$i] ?? '');
            $date   = $dates[$i] ?? date('Y-m-d');

            if ($test_id && $result) {
                $stmt->execute([$visit_id, $test_id, $result, $date, $user_id]);

                $costStmt = $pdo->prepare(
                    "SELECT cost FROM lab_tests WHERE lab_test_id = ?"
                );
                $costStmt->execute([$test_id]);
                $grandTotal += floatval($costStmt->fetchColumn() ?: 0);
            }
        }
    }

    /* ================= UPDATE TOTAL ================= */
    $stmt = $pdo->prepare("
        UPDATE patient_visits SET total_amount = ? WHERE visit_id = ?
    ");
    $stmt->execute([$grandTotal, $visit_id]);

    /* ================= ACTIVITY LOG ================= */
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (activity_type, description)
        VALUES ('patient', ?)
    ");
    $stmt->execute([
        "New visit for $full_name | Total TSh " . number_format($grandTotal)
    ]);

    $pdo->commit();

    echo "
    <script>
      alert('✅ Patient, medicines & lab tests saved successfully');
      window.location.href='../patient_registration_form.php';
    </script>";
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("❌ Error: " . $e->getMessage());
}

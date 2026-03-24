<?php
require "db.php";

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$id || !in_array($action, ['activate', 'deactivate'])) {
    die("Invalid request");
}

$status = ($action === 'activate') ? 1 : 0;

$stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
$stmt->execute([$status, $id]);

header("Location: ../view_users.php");
exit;

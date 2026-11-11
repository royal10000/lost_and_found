<?php
require_once "includes/db_connection.php";
require_once "includes/sessions.php";
require_once "includes/functions.php";

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:reported_item.php");
    exit();
}

$user = currentUser();
$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    message("Report link is not available.", "warning");
    header("Location:reported_item.php");
    exit();
}

$stmt = $pdo->prepare("SELECT image FROM reports WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$id, $user['id']]);
$report = $stmt->fetch();

if (!$report) {
    message("You can only delete your own reports.", "warning");
    header("Location:reported_item.php");
    exit();
}

$delete = $pdo->prepare("DELETE FROM reports WHERE id = ? AND user_id = ?");
$delete->execute([$id, $user['id']]);

message("Report deleted successfully.");
header("Location:reported_item.php");
exit();

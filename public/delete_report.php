<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/report.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reported_item.php');
    exit;
}

$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';
if (!is_string($submittedToken) || $sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
    header('Location: reported_item.php?delete=failed');
    exit;
}

$reportId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$reportId || $reportId < 1) {
    header('Location: reported_item.php?delete=failed');
    exit;
}

try {
    $deleted = (new Report())->deleteReport($reportId, (int) Session::currentUser());
    header('Location: reported_item.php?delete=' . ($deleted ? 'success' : 'failed'));
} catch (Throwable $exception) {
    header('Location: reported_item.php?delete=failed');
}
exit;

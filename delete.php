<?php
require_once "config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM job_applications WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?deleted=1");
exit;
?>
<?php
require_once __DIR__ . '/../app/init.php';

if (!empty($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("DELETE FROM trusted_devices WHERE user_id = ?");
    $stmt->execute([$userId]);
}

setcookie('device_token', '', time() - 3600, '/', '', false, true);

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;

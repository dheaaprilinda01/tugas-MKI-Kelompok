<?php
require __DIR__ . '/../connection/config.php';

$username = "admin";
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:u, :p)");
$stmt->execute([':u' => $username, ':p' => $hash]);

echo "User berhasil dibuat.";
?>
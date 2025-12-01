<?php
// tampilkan error kalau lagi debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/init.php';

try {
    $username = 'demo';
    $password = 'password123';
    $email    = 'demo@example.com';

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password_hash, $email]);

    $user_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO accounts (user_id, account_number, balance) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, '1234567890', 1000000.00]);

    echo "User demo berhasil dibuat!<br>";
    echo "Username: demo<br>";
    echo "Password: password123<br>";
} catch (Throwable $e) {
    echo "Terjadi error: " . $e->getMessage();
}
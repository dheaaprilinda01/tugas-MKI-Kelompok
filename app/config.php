<?php

$db_host = 'localhost';
$db_name = 'secure_ebanking'; // pastikan sama dengan nama DB di phpMyAdmin
$db_user = 'root';            // default XAMPP
$db_pass = '';                // kosong kalau kamu tidak pakai password

$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi keamanan
define('MAX_LOGIN_ATTEMPTS', 5);  
define('LOCKOUT_MINUTES', 15); 
define('OTP_EXP_MINUTES', 5);     

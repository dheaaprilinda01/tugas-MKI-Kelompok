<?php
session_start();
require __DIR__ . '/../connection/config.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Waktu lockout
$max_attempts   = 5;                 // jumlah maksimal salah
$cooldown_sec   = 300;               // 300 detik = 5 menit

// Ambil user dari DB
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
$stmt->execute([':u' => $username]);
$user = $stmt->fetch();

// Jika user tidak ditemukan
if (!$user) {
    http_response_code(401);
    echo "Username atau password salah.";
    exit;
}

// Cek apakah user sedang dikunci
if ($user['lock_until'] !== NULL) {
    $lock_until = strtotime($user['lock_until']);
    $now = time();

    if ($now < $lock_until) {
        $sisa = $lock_until - $now;
        echo "Akun terkunci. Coba lagi dalam {$sisa} detik.";
        exit;
    } else {
        // Reset lockout otomatis setelah waktu habis
        $pdo->prepare("UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE id = :id")
            ->execute([':id' => $user['id']]);
        $user['failed_attempts'] = 0;
        $user['lock_until'] = NULL;
    }
}

// Verifikasi password
if (!password_verify($password, $user['password_hash'])) {

    $attempts = $user['failed_attempts'] + 1;

    // Jika gagal & sudah melebihi batas
    if ($attempts >= $max_attempts) {
        $lock_time = date("Y-m-d H:i:s", time() + $cooldown_sec);

        $pdo->prepare(
            "UPDATE users SET failed_attempts = :a, lock_until = :lu WHERE id = :id"
        )->execute([
            ':a'  => $attempts,
            ':lu' => $lock_time,
            ':id' => $user['id']
        ]);

        echo "Terlalu banyak percobaan gagal. Akun terkunci selama {$cooldown_sec} detik.";
        exit;
    }

    // Jika gagal tapi belum mencapai batas
    $pdo->prepare("UPDATE users SET failed_attempts = :a WHERE id = :id")
        ->execute([':a' => $attempts, ':id' => $user['id']]);

    echo "Username atau password salah.";
    exit;
}

// ===== LOGIN SUKSES =====

// Reset gagal login & lock
$pdo->prepare("UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE id = :id")
    ->execute([':id' => $user['id']]);

// Set session
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];

session_regenerate_id(true);
header("Location: dashboard.php");
exit;

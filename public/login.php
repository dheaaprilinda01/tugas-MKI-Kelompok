<?php
require_once __DIR__ . '/../app/init.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$usernameInput = '';

function get_lock_status(PDO $pdo, int $userId): ?array {
    $stmt = $pdo->prepare("SELECT failed_attempts, locked_until FROM login_attempts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    return $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usernameInput === '' || $password === '') {
        $error = 'Silakan isi username dan kata sandi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$usernameInput]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Kombinasi username / kata sandi tidak sesuai.';
        } else {
            $lock = get_lock_status($pdo, (int)$user['id']);

            if ($lock && !empty($lock['locked_until']) && $lock['locked_until'] > date('Y-m-d H:i:s')) {
                $lockedUntil = date('d M Y H:i', strtotime($lock['locked_until']));
                $error = "Akun Anda untuk sementara dikunci sampai {$lockedUntil} karena percobaan masuk yang berulang.";
            } else {
                if (!password_verify($password, $user['password_hash'])) {
                    if ($lock) {
                        $failed = (int)$lock['failed_attempts'] + 1;
                        $lockedUntil = null;
                        if ($failed >= MAX_LOGIN_ATTEMPTS) {
                            $lockedUntil = date('Y-m-d H:i:s', time() + LOCKOUT_MINUTES * 60);
                        }

                        $stmt = $pdo->prepare("
                            UPDATE login_attempts
                            SET failed_attempts = ?, locked_until = ?, last_attempt_at = NOW()
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$failed, $lockedUntil, $user['id']]);
                    } else {
                        $failed = 1;
                        $lockedUntil = null;
                        $stmt = $pdo->prepare("
                            INSERT INTO login_attempts (user_id, failed_attempts, locked_until, last_attempt_at)
                            VALUES (?, ?, ?, NOW())
                        ");
                        $stmt->execute([$user['id'], $failed, $lockedUntil]);
                    }

                    if ($failed >= MAX_LOGIN_ATTEMPTS && $lockedUntil) {
                        $error = 'Akun Anda untuk sementara dikunci karena percobaan masuk yang berulang. Silakan coba kembali beberapa saat lagi.';
                    } else {
                        $sisa = MAX_LOGIN_ATTEMPTS - $failed;
                        $error = 'Kombinasi username / kata sandi tidak sesuai.'
                               . ($sisa > 0 ? " Percobaan tersisa: {$sisa}." : '');
                    }
                } else {
                    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE user_id = ?");
                    $stmt->execute([$user['id']]);

                    $needRegisterDevice = true;
                    if (!empty($_COOKIE['device_token'])) {
                        $deviceToken = $_COOKIE['device_token'];
                        $stmt = $pdo->prepare("
                            SELECT id FROM trusted_devices
                            WHERE user_id = ? AND device_token = ?
                            LIMIT 1
                        ");
                        $stmt->execute([$user['id'], $deviceToken]);
                        $trusted = $stmt->fetch();
                        if ($trusted) {
                            $needRegisterDevice = false;
                        }
                    }

                    $stmt = $pdo->prepare("DELETE FROM otp_codes WHERE user_id = ? AND is_used = 0");
                    $stmt->execute([$user['id']]);

                    $otp       = random_int(100000, 999999);
                    $expiresAt = date('Y-m-d H:i:s', time() + OTP_EXP_MINUTES * 60);

                    $stmt = $pdo->prepare("
                        INSERT INTO otp_codes (user_id, code, expires_at)
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$user['id'], $otp, $expiresAt]);

                    $_SESSION['pending_user_id']       = $user['id'];
                    $_SESSION['device_needs_register'] = $needRegisterDevice;


                    header('Location: mfa.php');
                    exit;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Masuk | Ocean Bank Internet Banking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <style>
        :root {
            --gold-soft: #fbbf24;
            --gold-strong: #eab308;
            --blue-deep: #020617;
            --blue-light: #1d4ed8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(250, 250, 255, 0.45), transparent 55%),
                radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.25), transparent 55%),
                linear-gradient(135deg, #0f172a, #020617);
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-shell {
            max-width: 960px;
            width: 100%;
            background-color: rgba(15, 23, 42, 0.96);
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 24px 60px rgba(15, 23, 42, 0.55),
                0 0 0 1px rgba(148, 163, 184, 0.20);
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
        }

        @media (max-width: 768px) {
            body {
                padding: 16px;
            }

            .login-shell {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .left-pane {
            padding: 28px 32px 32px;
            background: radial-gradient(circle at top left, #0b1120, #020617 55%, #1d4ed8 100%);
            color: #e5e7eb;
        }

        .logo-badge {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            background: linear-gradient(145deg, var(--gold-soft), var(--gold-strong));
            color: #111827;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.55);
        }

        .bank-title {
            font-size: 18px;
            font-weight: 600;
            color: #f9fafb;
        }

        .bank-subtitle {
            font-size: 13px;
            color: #cbd5f5;
        }

        .tagline-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid rgba(248, 250, 252, 0.4);
            font-size: 12px;
            color: #e5e7eb;
            margin-top: 18px;
            margin-bottom: 28px;
        }

        .tagline-pill span {
            margin-left: 6px;
            font-weight: 500;
        }

        .welcome-title {
            font-size: 30px;
            line-height: 1.25;
            font-weight: 700;
            color: #facc15; /* kuning emas */
            margin-bottom: 14px;
        }

        .welcome-text {
            font-size: 14px;
            color: #e5e7eb;
            max-width: 420px;
        }

        .feature-list {
            margin-top: 22px;
            padding-left: 0;
            list-style: none;
            font-size: 13px;
            color: #e5e7eb;
        }

        .feature-list li {
            margin-bottom: 6px;
            display: flex;
            align-items: flex-start;
        }

        .feature-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background-color: #facc15;
            margin-right: 8px;
            margin-top: 6px;
        }

        .left-footer {
            margin-top: 32px;
            font-size: 11px;
            color: #9ca3af;
        }

        .right-pane {
            padding: 28px 32px 32px;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
        }

        .right-header {
            margin-bottom: 16px;
        }

        .right-header h5 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .right-header p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 0;
        }

        .form-label {
            font-size: 13px;
            margin-bottom: 4px;
            color: #374151;
        }

        .form-control {
            font-size: 14px;
        }

        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .toggle-password {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .small-note {
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="login-shell">
    <!-- LEFT PANEL -->
    <section class="left-pane">
        <div class="d-flex align-items-center gap-2">
            <div class="logo-badge">OB</div>
            <div>
                <div class="bank-title">Ocean Bank</div>
                <div class="bank-subtitle">Internet Banking</div>
            </div>
        </div>

        <div class="tagline-pill">
            <span>Akses keuangan Anda, kapan saja.</span>
        </div>

        <h1 class="welcome-title">Selamat datang kembali di Ocean Bank.</h1>

        <p class="welcome-text">
            Kelola rekening, pantau saldo, dan lakukan transaksi harian dengan aman melalui layanan internet banking kami.
        </p>

        <ul class="feature-list">
            <li>
                <div class="feature-dot"></div>
                <div>Autentikasi berlapis untuk setiap proses masuk.</div>
            </li>
            <li>
                <div class="feature-dot"></div>
                <div>Pencatatan transaksi yang dapat ditelusuri.</div>
            </li>
            <li>
                <div class="feature-dot"></div>
                <div>Akses dari perangkat yang telah terverifikasi.</div>
            </li>
        </ul>

        <div class="left-footer">
            © <?= date('Y') ?> Ocean Bank. Seluruh hak dilindungi.
        </div>
    </section>

    <!-- RIGHT PANEL -->
    <section class="right-pane">
        <div class="right-header">
            <h5>Masuk ke Layanan</h5>
            <p>Silakan masukkan kredensial yang telah terdaftar untuk melanjutkan.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger small py-2"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" autocomplete="off" class="mt-1">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    value="<?= e($usernameInput) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        required
                    >
                    <button class="btn btn-outline-secondary toggle-password" type="button" id="togglePassword">
                        Tampilkan
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2">Masuk</button>

            <p class="small-note mt-3 mb-0">
                Jangan berikan username, kata sandi, maupun kode otentikasi kepada pihak mana pun,
                termasuk yang mengatasnamakan petugas bank.
            </p>
        </form>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script>
    // Toggle show/hide password
    const toggleBtn = document.getElementById('togglePassword');
    const pwdInput = document.getElementById('password');

    toggleBtn.addEventListener('click', function () {
        const type = pwdInput.getAttribute('type') === 'password' ? 'text' : 'password';
        pwdInput.setAttribute('type', type);
        this.textContent = type === 'password' ? 'Tampilkan' : 'Sembunyikan';
    });
</script>
</body>
</html>
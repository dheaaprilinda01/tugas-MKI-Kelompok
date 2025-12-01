<?php
require_once __DIR__ . '/../app/init.php';

if (empty($_SESSION['pending_user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['pending_user_id'];
$needRegisterDevice = !empty($_SESSION['device_needs_register']);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['otp'] ?? '');

    if ($code === '') {
        $error = 'Silakan masukkan kode otentikasi.';
    } else {
        $now = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("SELECT * FROM otp_codes 
                               WHERE user_id = ? AND code = ? AND is_used = 0 AND expires_at >= ?
                               ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$user_id, $code, $now]);
        $otpRow = $stmt->fetch();

        if (!$otpRow) {
            $error = 'Kode otentikasi tidak valid atau sudah tidak berlaku.';
        } else {
            // tandai OTP terpakai
            $stmt = $pdo->prepare("UPDATE otp_codes SET is_used = 1 WHERE id = ?");
            $stmt->execute([$otpRow['id']]);

            // register device jika perlu
            if ($needRegisterDevice) {
                $deviceToken = bin2hex(random_bytes(32));
                $deviceName  = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Perangkat', 0, 200);

                $stmt = $pdo->prepare("INSERT INTO trusted_devices (user_id, device_token, device_name) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $deviceToken, $deviceName]);

                setcookie('device_token', $deviceToken, time() + (365*24*60*60), '/', '', false, true);
            }

            unset($_SESSION['pending_user_id'], $_SESSION['device_needs_register']);
            $_SESSION['user_id'] = $user_id;

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Kode | Ocean Bank</title>
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
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(250, 250, 255, 0.45), transparent 55%),
                radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.25), transparent 55%),
                linear-gradient(135deg, #020617, #0f172a 40%, #1d4ed8 70%, #38bdf8 100%);
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;

            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .mfa-card {
            max-width: 420px;
            width: 100%;
            animation: fadeUp 0.5s ease-out;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
</head>
<body>
<div class="card shadow border-0 rounded-4 mfa-card">
    <div class="card-body p-4">
        <!-- Header yg lama (logo 2F + teks) DIHAPUS -->

        <h5 class="mb-1">Masukkan kode otentikasi</h5>
        <p class="text-muted small mb-3">
            Kami telah mengirimkan kode otentikasi satu kali (OTP) ke kanal yang terdaftar.
            Masukkan 6 digit kode tersebut untuk menyelesaikan proses masuk.
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small mb-3"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="mfa.php" autocomplete="off">
            <div class="mb-3">
                <label for="otp" class="form-label small mb-1">Kode 6 digit</label>
                <input
                    type="text"
                    class="form-control text-center fs-4"
                    id="otp"
                    name="otp"
                    maxlength="6"
                    pattern="\d*"
                    inputmode="numeric"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-sm">
                Verifikasi &amp; Masuk
            </button>
        </form>

        <p class="text-muted small mt-3 mb-0">
            Jika Anda tidak merasa melakukan proses masuk ini, segera hubungi layanan resmi Ocean Bank.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
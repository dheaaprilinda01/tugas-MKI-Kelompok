<?php
require_once __DIR__ . '/../app/init.php';
require_login();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT account_number FROM accounts WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

$stmt = $pdo->prepare("SELECT device_name, created_at FROM trusted_devices WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$devices = $stmt->fetchAll();

$registered = !empty($user['created_at']) ? date('d M Y H:i', strtotime($user['created_at'])) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Nasabah | Ocean Bank</title>
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
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(250, 250, 255, 0.45), transparent 55%),
                radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.25), transparent 55%),
                linear-gradient(135deg, #eef4ff, #e0f2fe);
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .navbar {
            background-color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        .logo-gold {
            background: linear-gradient(145deg, var(--gold-soft), var(--gold-strong));
            color: #111827;
        }

        .main-wrapper {
            max-width: 1120px;
            margin: 0 auto;
            padding: 24px 16px 32px;
        }

        .card-soft {
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light border-bottom">
    <div class="container" style="max-width: 1120px;">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <div class="logo-gold rounded-3 fw-bold d-flex align-items-center justify-content-center me-2"
                 style="width:34px;height:34px;">
                OB
            </div>
            <div>
                <div class="fw-semibold">Ocean Bank</div>
                <div class="small text-muted">Digital Internet Banking</div>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3 small">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="transfer.php">Transfer</a></li>
                <li class="nav-item"><a class="nav-link" href="transactions.php">Mutasi</a></li>
                <li class="nav-item"><a class="nav-link active" href="profile.php">Profil</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3 small">
                <div class="text-end">
                    <div class="fw-semibold"><?= e($user['username']) ?></div>
                    <div class="text-muted"><?= e($user['email']) ?></div>
                </div>
                <a href="logout.php" class="btn btn-outline-secondary btn-sm">Keluar</a>
            </div>
        </div>
    </div>
</nav>

<main class="main-wrapper">
    <header class="mb-3">
        <h5 class="mb-1">Profil Nasabah</h5>
        <p class="text-muted small mb-0">
            Ringkasan data yang digunakan untuk layanan internet banking dan pengaturan keamanan akses.
        </p>
    </header>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card card-soft">
                <div class="card-body p-3 p-md-3">
                    <h6 class="mb-2">Data Profil</h6>
                    <div class="row small">
                        <div class="col-6 mb-2">
                            <div class="text-uppercase text-muted mb-0" style="font-size:.7rem;">Nama Pengguna</div>
                            <div><?= e($user['username']) ?></div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-uppercase text-muted mb-0" style="font-size:.7rem;">Alamat Email</div>
                            <div><?= e($user['email']) ?></div>
                        </div>
                        <?php if ($account): ?>
                            <div class="col-6 mb-2">
                                <div class="text-uppercase text-muted mb-0" style="font-size:.7rem;">Rekening Terhubung</div>
                                <div><?= e($account['account_number']) ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($registered): ?>
                            <div class="col-6 mb-2">
                                <div class="text-uppercase text-muted mb-0" style="font-size:.7rem;">Aktif Sejak</div>
                                <div><?= e($registered) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted small mb-0">
                        Untuk perubahan data profil utama, silakan hubungi kantor cabang atau layanan resmi Ocean Bank.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-soft">
                <div class="card-body p-3 p-md-3">
                    <h6 class="mb-2">Keamanan Akses</h6>
                    <ul class="small text-muted mb-2">
                        <li>Login dilindungi dengan kata sandi dan kode otentikasi satu kali (OTP).</li>
                        <li>Akun akan dikunci sementara jika terdapat percobaan masuk berulang yang gagal.</li>
                        <li>Akses dari perangkat baru akan diminta verifikasi sebelum menjadi perangkat tepercaya.</li>
                    </ul>
                    <p class="small mb-0 text-muted">
                        Jangan membagikan kredensial maupun kode otentikasi kepada pihak mana pun,
                        termasuk yang mengatasnamakan petugas bank.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card card-soft">
                <div class="card-body p-3 p-md-3">
                    <h6 class="mb-2">Perangkat Tepercaya</h6>
                    <?php if (!$devices): ?>
                        <p class="text-muted small mb-0">
                            Belum terdapat perangkat yang tercatat sebagai perangkat tepercaya.
                            Perangkat pertama akan tercatat setelah proses masuk berhasil.
                        </p>
                    <?php else: ?>
                        <div class="table-responsive small mb-0">
                            <table class="table table-sm align-middle">
                                <thead>
                                <tr>
                                    <th>Nama Perangkat</th>
                                    <th>Terdaftar Pada</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($devices as $d): ?>
                                    <tr>
                                        <td><?= e($d['device_name']) ?></td>
                                        <td><?= e(date('d M Y H:i', strtotime($d['created_at']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            Jika Anda tidak mengenali salah satu perangkat di atas, disarankan untuk mengganti kata sandi akun.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
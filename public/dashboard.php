<?php
require_once __DIR__ . '/../app/init.php';
require_login();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT id, account_number, balance FROM accounts WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

$registered = !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : null;

// ambil 5 transaksi terakhir untuk rekening ini
$transactions = [];
if ($account) {
    $stmt = $pdo->prepare("
        SELECT t.*, 
               fa.account_number AS from_number,
               ta.account_number AS to_number
        FROM transactions t
        LEFT JOIN accounts fa ON t.from_account_id = fa.id
        LEFT JOIN accounts ta ON t.to_account_id = ta.id
        WHERE t.from_account_id = :acc_id OR t.to_account_id = :acc_id
        ORDER BY t.created_at DESC
        LIMIT 5
    ");
    $stmt->execute(['acc_id' => $account['id']]);
    $transactions = $stmt->fetchAll();
}

$hour = (int) date('H');
if     ($hour < 11) $greet = 'Selamat pagi';
elseif ($hour < 15) $greet = 'Selamat siang';
elseif ($hour < 19) $greet = 'Selamat sore';
else                $greet = 'Selamat malam';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Ocean Bank Digital Internet Banking</title>
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
            animation: fadeUp 0.4s ease-out;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-soft {
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .card-balance {
            background: radial-gradient(circle at top left, #1d4ed8, #020617 70%);
            color: #e5e7eb;
            border: none;
            min-height: 220px;
        }

        .card-balance .badge {
            background-color: rgba(15, 23, 42, 0.9);
            color: #e5e7eb;
        }

        .card-quick-actions {
            min-height: 220px;
        }

        .quick-icon {
            width: 34px;
            height: 34px;
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
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="transfer.php">Transfer</a></li>
                <li class="nav-item"><a class="nav-link" href="transactions.php">Mutasi</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
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
    <!-- Greeting -->
    <header class="mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-1"><?= $greet ?>, <?= e($user['username']) ?>.</h5>
                <p class="text-muted small mb-0">
                    Ringkasan layanan Ocean Bank untuk rekening yang terhubung dengan profil Anda.
                    <?php if ($registered): ?>
                        Profil aktif sejak <?= e($registered) ?>.
                    <?php endif; ?>
                </p>
            </div>
            <div class="small text-muted">
                <?= date('l, d M Y H:i') ?>
            </div>
        </div>
    </header>

    <!-- Top row -->
    <div class="row g-3 mb-3">
        <!-- Balance -->
        <div class="col-lg-7">
            <div class="card card-balance rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="small mb-1">Saldo Rekening Utama</div>
                            <div class="fs-3 fw-semibold">
                                <?php if ($account): ?>
                                    Rp <?= number_format($account['balance'], 2, ',', '.') ?>
                                <?php else: ?>
                                    Rp 0,00
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($account): ?>
                            <div class="text-end small">
                                <div class="text-light mb-1">No. Rekening</div>
                                <span class="badge rounded-pill">
                                    <?= e($account['account_number']) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="small text-light mb-0">
                        Informasi saldo ditampilkan berdasarkan data terakhir yang tercatat di sistem.
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="col-lg-5">
            <div class="card card-soft card-quick-actions">
                <div class="card-body p-3 p-md-3 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Tindakan Cepat</h6>
                        <span class="badge text-bg-light small">Akses utama</span>
                    </div>

                    <div class="row g-2 small flex-grow-1">
                        <div class="col-6">
                            <a href="transfer.php" class="text-decoration-none text-dark">
                                <div class="border rounded-3 p-2 d-flex align-items-center gap-2 bg-white h-100">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center quick-icon">
                                        <span class="text-primary fw-semibold">⇄</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Transfer</div>
                                        <div class="text-muted">Antar rekening</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="transactions.php" class="text-decoration-none text-dark">
                                <div class="border rounded-3 p-2 d-flex align-items-center gap-2 bg-white h-100">
                                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center quick-icon">
                                        <span class="text-info fw-semibold">⟲</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Mutasi</div>
                                        <div class="text-muted">Riwayat transaksi</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="profile.php" class="text-decoration-none text-dark">
                                <div class="border rounded-3 p-2 d-flex align-items-center gap-2 bg-white h-100">
                                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center quick-icon">
                                        <span class="text-warning fw-semibold">👤</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Profil</div>
                                        <div class="text-muted">Data & keamanan</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-3 p-2 d-flex align-items-center gap-2 bg-white h-100">
                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center quick-icon">
                                    <span class="text-success fw-semibold">✓</span>
                                </div>
                                <div>
                                    <div class="fw-semibold">Pembayaran</div>
                                    <div class="text-muted">Segera tersedia</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted small mb-0 mt-2">
                        Layanan lain tersedia sesuai ketentuan Ocean Bank dan dapat diakses melalui menu navigasi.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom row: hanya transaksi -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card card-soft">
                <div class="card-body p-3 p-md-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Transaksi Terbaru</h6>
                        <a href="transactions.php" class="small text-decoration-none">Lihat semua</a>
                    </div>

                    <?php if (!$transactions): ?>
                        <p class="text-muted small mb-0">
                            Belum terdapat transaksi yang tercatat untuk rekening ini.
                        </p>
                    <?php else: ?>
                        <ul class="list-unstyled small mb-0">
                            <?php foreach ($transactions as $t): ?>
                                <?php
                                $isDebit = ($t['from_account_id'] == $account['id']);
                                $amount  = number_format($t['amount'], 2, ',', '.');
                                $sign    = $isDebit ? '-' : '+';
                                $class   = $isDebit ? 'text-danger' : 'text-success';
                                $timeStr = date('d M H:i', strtotime($t['created_at']));
                                $ket     = $t['description'] ?: 'Transaksi';
                                ?>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <div>
                                        <div><?= e($ket) ?></div>
                                        <div class="text-muted"><?= e($timeStr) ?></div>
                                    </div>
                                    <div class="text-end <?= $class ?>">
                                        <?= $sign . ' ' . $amount ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="text-muted small mt-2 mb-0">
                            Menampilkan hingga 5 transaksi terakhir yang tercatat.
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
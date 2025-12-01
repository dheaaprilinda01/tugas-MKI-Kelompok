<?php
require_once __DIR__ . '/../app/init.php';
require_login();

$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Ambil rekening user (anggap 1 rekening utama)
$stmt = $pdo->prepare("SELECT id, account_number FROM accounts WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

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
    ");
    $stmt->execute(['acc_id' => $account['id']]);
    $transactions = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mutasi Rekening | Ocean Bank</title>
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

        .table-sm th,
        .table-sm td {
            padding-top: .45rem;
            padding-bottom: .45rem;
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
                <li class="nav-item"><a class="nav-link active" href="transactions.php">Mutasi</a></li>
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
    <header class="mb-3">
        <h5 class="mb-1">Mutasi Rekening</h5>
        <?php if ($account): ?>
            <p class="text-muted small mb-0">
                Riwayat transaksi untuk rekening <strong><?= e($account['account_number']) ?></strong>.
            </p>
        <?php else: ?>
            <p class="text-muted small mb-0">
                Rekening belum terdaftar untuk profil ini.
            </p>
        <?php endif; ?>
    </header>

    <div class="card card-soft">
        <div class="card-body p-3 p-md-3">
            <?php if (!$account): ?>
                <p class="text-muted small mb-0">
                    Tidak dapat menampilkan mutasi karena belum ada rekening yang terhubung.
                </p>
            <?php elseif (!$transactions): ?>
                <p class="text-muted small mb-0">
                    Belum terdapat transaksi yang tercatat untuk rekening ini.
                </p>
            <?php else: ?>
                <div class="table-responsive small">
                    <table class="table table-sm align-middle">
                        <thead>
                        <tr>
                            <th>Tanggal & Waktu</th>
                            <th>Keterangan</th>
                            <th>Dari</th>
                            <th>Ke</th>
                            <th class="text-end">Jumlah (Rp)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($transactions as $t): ?>
                            <?php
                            $isDebit = ($t['from_account_id'] == $account['id']);
                            $amount  = number_format($t['amount'], 2, ',', '.');
                            $class   = $isDebit ? 'text-danger' : 'text-success';
                            $sign    = $isDebit ? '-' : '+';
                            $ket     = $t['description'] ?: 'Transaksi';
                            $tgl     = date('d M Y H:i', strtotime($t['created_at']));
                            ?>
                            <tr>
                                <td><?= e($tgl) ?></td>
                                <td><?= e($ket) ?></td>
                                <td><?= e($t['from_number'] ?? '-') ?></td>
                                <td><?= e($t['to_number'] ?? '-') ?></td>
                                <td class="text-end <?= $class ?>">
                                    <?= $sign . ' ' . $amount ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mt-2 mb-0">
                    Data transaksi ditampilkan berdasarkan catatan terakhir pada sistem Ocean Bank.
                </p>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
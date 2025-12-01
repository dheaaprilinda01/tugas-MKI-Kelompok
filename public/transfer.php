<?php
require_once __DIR__ . '/../app/init.php';
require_login();

$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Ambil rekening user (anggap satu rekening utama)
$stmt = $pdo->prepare("SELECT id, account_number, balance FROM accounts WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

$success = '';
$error   = '';
$targetAccountName = null;
$targetAccountNumberInput = '';
$amountInput = '';
$descriptionInput = '';

if ($account && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetAccountNumberInput = trim($_POST['target_account'] ?? '');
    $amountInput             = trim($_POST['amount'] ?? '');
    $descriptionInput        = trim($_POST['description'] ?? '');

    if ($targetAccountNumberInput === '' || $amountInput === '') {
        $error = 'Nomor rekening tujuan dan jumlah harus diisi.';
    } else {
        // amount numeric
        $amount = str_replace(['.', ','], ['', '.'], $amountInput); // simple normalize
        if (!is_numeric($amount) || $amount <= 0) {
            $error = 'Jumlah tidak valid.';
        } else {
            // cari rekening tujuan
            $stmt = $pdo->prepare("SELECT id, account_number, user_id, balance FROM accounts WHERE account_number = ? LIMIT 1");
            $stmt->execute([$targetAccountNumberInput]);
            $targetAccount = $stmt->fetch();

            if (!$targetAccount) {
                $error = 'Rekening tujuan tidak ditemukan.';
            } elseif ($targetAccount['id'] == $account['id']) {
                $error = 'Tidak dapat mentransfer ke rekening yang sama.';
            } elseif ($amount > $account['balance']) {
                $error = 'Saldo tidak mencukupi untuk melakukan transfer.';
            } else {
                // opsional: ambil nama pemilik tujuan
                $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->execute([$targetAccount['user_id']]);
                $targetOwner = $stmt->fetch();
                $targetAccountName = $targetOwner ? $targetOwner['username'] : null;

                // mulai transaksi database
                $pdo->beginTransaction();
                try {
                    // kurangi saldo pengirim
                    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
                    $stmt->execute([$amount, $account['id']]);

                    // tambah saldo penerima
                    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
                    $stmt->execute([$amount, $targetAccount['id']]);

                    // catat transaksi
                    $stmt = $pdo->prepare("
                        INSERT INTO transactions (from_account_id, to_account_id, amount, description, created_at)
                        VALUES (?, ?, ?, ?, NOW())
                    ");
                    $desc = $descriptionInput !== '' ? $descriptionInput : 'Transfer ke ' . $targetAccount['account_number'];
                    $stmt->execute([$account['id'], $targetAccount['id'], $amount, $desc]);

                    $pdo->commit();

                    $success = 'Transfer berhasil dikirim ke rekening ' . e($targetAccount['account_number']) .
                               ($targetAccountName ? ' a.n. ' . e($targetAccountName) : '') . '.';

                    // refresh saldo terbaru
                    $stmt = $pdo->prepare("SELECT id, account_number, balance FROM accounts WHERE id = ? LIMIT 1");
                    $stmt->execute([$account['id']]);
                    $account = $stmt->fetch();

                    // kosongkan input jumlah & keterangan
                    $amountInput = '';
                    $descriptionInput = '';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = 'Terjadi kesalahan saat memproses transfer. Silakan coba kembali.';
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
    <title>Transfer Dana | Ocean Bank</title>
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

        .badge-account {
            background-color: #0f172a;
            color: #e5e7eb;
            border-radius: 999px;
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
                <li class="nav-item"><a class="nav-link active" href="transfer.php">Transfer</a></li>
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
    <header class="mb-3">
        <h5 class="mb-1">Transfer Dana</h5>
        <p class="text-muted small mb-0">
            Lakukan pemindahan dana ke rekening lain secara aman melalui layanan Ocean Bank.
        </p>
    </header>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card card-soft">
                <div class="card-body p-3 p-md-3">
                    <h6 class="mb-2">Rekening Sumber</h6>

                    <?php if (!$account): ?>
                        <p class="text-muted small mb-0">
                            Belum terdapat rekening yang terhubung dengan profil Anda.
                            Transfer tidak dapat dilakukan.
                        </p>
                    <?php else: ?>
                        <div class="mb-3 small">
                            <div class="text-uppercase text-muted mb-0" style="font-size:.7rem;">Nomor Rekening</div>
                            <span class="badge badge-account px-3 py-1">
                                <?= e($account['account_number']) ?>
                            </span>
                        </div>
                        <div class="mb-1 small">
                            <div class="text-uppercase text-muted mb-0" style="font-size:.7rem;">Saldo Tersedia</div>
                            <div class="fw-semibold">
                                Rp <?= number_format($account['balance'], 2, ',', '.') ?>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">
                            Pastikan saldo mencukupi sebelum melakukan transfer. Biaya administrasi (jika ada) akan
                            diinformasikan sesuai ketentuan bank.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-soft">
                <div class="card-body p-3 p-md-3">
                    <h6 class="mb-2">Detail Transfer</h6>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small"><?= e($error) ?></div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success py-2 small"><?= e($success) ?></div>
                    <?php endif; ?>

                    <?php if ($account): ?>
                        <form method="post" action="transfer.php" autocomplete="off" class="small">
                            <div class="mb-2">
                                <label for="target_account" class="form-label mb-1">Rekening tujuan</label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="target_account"
                                    name="target_account"
                                    value="<?= e($targetAccountNumberInput) ?>"
                                    required
                                >
                                <?php if ($targetAccountName): ?>
                                    <div class="form-text">
                                        a.n. <?= e($targetAccountName) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-2">
                                <label for="amount" class="form-label mb-1">Jumlah (Rp)</label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="amount"
                                    name="amount"
                                    value="<?= e($amountInput) ?>"
                                    required
                                >
                            </div>

                            <div class="mb-2">
                                <label for="description" class="form-label mb-1">Keterangan (opsional)</label>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="description"
                                    name="description"
                                    value="<?= e($descriptionInput) ?>"
                                    placeholder="Misal: Pembayaran, Kirim dana, dll."
                                >
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm mt-2 w-100">
                                Kirim Transfer
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted small mb-0">
                            Data rekening sumber belum tersedia.
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
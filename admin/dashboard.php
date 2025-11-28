<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<h1>Halo, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
<p>Anda berhasil login.</p>
<a href="logout.php">Logout</a>

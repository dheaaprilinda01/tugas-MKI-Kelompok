<?php

session_start();

require_once __DIR__ . '/config.php';

/**
 * Wajib login sebelum akses halaman tertentu
 */
function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: ../public/login.php');
        exit;
    }
}

/**
 * Escape HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

/**
 * Mengirim email OTP MFA ke pengguna.
 *
 * @param string $toEmail   Alamat email tujuan
 * @param string $toName    Nama penerima (username)
 * @param string $otpCode   Kode OTP (6 digit)
 * @param int    $validMinutes Berapa menit OTP berlaku
 * @return bool  true jika terkirim, false jika gagal
 */
function send_mfa_email(string $toEmail, string $toName, string $otpCode, int $validMinutes = 5): bool
{
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // GANTI INI dengan email & app password Gmail kamu
        $mail->Username   = 'EMAIL_GMAIL_KAMU@gmail.com';
        $mail->Password   = 'APP_PASSWORD_GMAIL_KAMU';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Pengirim
        $mail->setFrom('EMAIL_GMAIL_KAMU@gmail.com', 'Ocean Bank Internet Banking');

        // Penerima
        $mail->addAddress($toEmail, $toName);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = 'Kode Verifikasi Ocean Bank Anda';
        $mail->Body = "
            <p>Halo <strong>" . htmlspecialchars($toName, ENT_QUOTES, 'UTF-8') . "</strong>,</p>
            <p>Kode otentikasi satu kali (OTP) Ocean Bank Anda adalah:</p>
            <p style='font-size:28px; letter-spacing:6px; font-weight:bold;'>
                {$otpCode}
            </p>
            <p>Kode ini berlaku selama <strong>{$validMinutes} menit</strong>. Jangan berikan kode ini kepada siapa pun.</p>
            <br>
            <p>Hormat kami,<br>Ocean Bank Digital Internet Banking</p>
        ";

        $mail->AltBody = "Kode OTP Ocean Bank Anda: {$otpCode} (berlaku {$validMinutes} menit).";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Kalau mau debug, bisa sementara: error_log($e->getMessage());
        return false;
    }
}

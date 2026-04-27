<?php
include '../config/koneksi.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/phpmailer/Exception.php';
require '../assets/phpmailer/PHPMailer.php';
require '../assets/phpmailer/SMTP.php';

$pesan = '';

if (isset($_POST['kirim'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $cek = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    $user = mysqli_fetch_assoc($cek);

    if ($user) {

        $kode = rand(100000, 999999);

        mysqli_query($conn, "
            UPDATE user 
            SET kode_verifikasi='$kode'
            WHERE email='$email'
        ");

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'meifriska13@gmail.com';
            $mail->Password = 'tsbvwlprllogcthz';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('meifriska13@gmail.com', 'ULPR DPRD JATIM');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Password';
            $mail->Body = "<h3>Kode Reset Password: $kode</h3>";

            $mail->send();

            header("Location: verifikasi_lupa.php?email=$email");
            exit;

        } catch (Exception $e) {
            $pesan = "Gagal kirim email!";
        }

    } else {
        $pesan = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Lupa Kata Sandi</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/login.css"> <!-- reuse style -->

</head>

<body>

<div class="wrapper">
<div class="main-card row g-0">

<!-- LEFT -->
<div class="col-md-6 left d-flex flex-column justify-content-between">
    <div>
        <h5>DPRD JATIM</h5>
        <h1>Pemulihan Akses Layanan.</h1>
        <p>Kami membantu Anda memulihkan akses ke sistem informasi permohonan rekomendasi DPRD Jawa Timur dengan aman dan cepat.</p>
    </div>

    <small>ℹ️ Jika Anda tidak lagi memiliki akses ke email instansi, silakan hubungi administrator IT kami.</small>
</div>

<!-- RIGHT -->
<div class="col-md-6 right">

    <h3>Lupa Kata Sandi?</h3>
    <p class="text-muted">
        Masukkan alamat email instansi yang terdaftar. Kami akan mengirimkan instruksi untuk mengatur ulang kata sandi Anda.
    </p>

    <?php if ($pesan): ?>
        <div class="alert alert-danger"><?= $pesan ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3 mt-4">
            <label>Email Instansi</label>
            <input type="email" name="email" class="form-control"
                   placeholder="admin@instansi.go.id" required>
        </div>

        <button name="kirim" class="btn btn-primary w-100">
            Kirim Tautan Pemulihan →
        </button>

        <div class="text-center mt-4">
            Ingat kata sandi Anda?
            <a href="login.php" class="text-decoration-none">← Kembali ke Login</a>
        </div>

    </form>

</div>
</div>
</div>

<div class="footer text-center mt-3">
© 2024 UNIT LAYANAN PERMOHONAN REKOMENDASI DPRD PROVINSI JAWA TIMUR
</div>

</body>
</html>
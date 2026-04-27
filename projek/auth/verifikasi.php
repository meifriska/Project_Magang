<?php
session_start();
include '../config/koneksi.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/phpmailer/Exception.php';
require '../assets/phpmailer/PHPMailer.php';
require '../assets/phpmailer/SMTP.php';

// ambil dari session
$data = $_SESSION['register_temp'] ?? null;

if (!$data) {
    header("Location: registrasi.php");
    exit;
}

$email = $data['email'];
$error = "";

/* =========================
   RESEND
========================= */
if (isset($_GET['resend'])) {

    $kode = rand(100000, 999999);
    $_SESSION['register_temp']['kode'] = $kode;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'meifriska13@gmail.com';
        $mail->Password = 'tsbvwlprllogcthz';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('meifriska13@gmail.com', 'ULPR DPRD');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Kode Baru';
        $mail->Body = "<h2>Kode Baru: $kode</h2>";

        $mail->send();

    } catch (Exception $e) {
        echo $mail->ErrorInfo;
    }

    header("Location: verifikasi.php");
    exit;
}

/* =========================
   VERIFIKASI
========================= */
if (isset($_POST['verifikasi'])) {

    $kode = $_POST['kode1'].$_POST['kode2'].$_POST['kode3'].$_POST['kode4'].$_POST['kode5'].$_POST['kode6'];

    if ($kode == $data['kode']) {

        // simpan ke DB
        mysqli_query($conn, "
        INSERT INTO user
        (nama_lengkap, nip, email, username, pass, id_instansi, status_akun)
        VALUES
        (
            '".$data['nama']."',
            '".$data['nip']."',
            '".$data['email']."',
            '".$data['username']."',
            '".password_hash($data['password'], PASSWORD_DEFAULT)."',
            '".$data['id_instansi']."',
            'aktif'
        )
        ");

        unset($_SESSION['register_temp']);

        echo "<script>alert('Berhasil!'); window.location='login.php';</script>";

    } else {
        $error = "Kode salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Verifikasi Kode</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/verifikasi.css">
</head>

<body>

<!-- NAVBAR -->
<div class="topbar d-flex justify-content-between align-items-center px-4 py-3">
    <div class="d-flex align-items-center gap-2">
        <div style="width:35px;height:35px;background:#4b2aad;border-radius:8px;"></div>
        <div>
            <strong>ULPR DPRD JATIM</strong><br>
            <small>Layanan Rekomendasi</small>
        </div>
    </div>

    <div>
        <span class="me-2 text-muted">Sudah punya akun?</span>
        <a href="login.php" class="btn btn-light btn-sm rounded-pill px-3">Masuk</a>
    </div>
</div>

<!-- CONTENT -->
<div class="wrapper">
<div class="main-card row g-0">

<!-- LEFT -->
<div class="col-md-5 left d-flex flex-column justify-content-between">
    <div>
        <h5>PENDAFTARAN AKUN</h5>
        <h1>Mulai Langkah Strategis Anda.</h1>
        <p>Bergabunglah dengan sistem layanan rekomendasi DPRD Jawa Timur yang transparan dan akuntabel.</p>
    </div>
    <small>🔒 Sistem Keamanan Terenkripsi</small>
</div>

<!-- RIGHT -->
<div class="col-md-7 right">

    <h4 class="mb-2">Verifikasi Kode</h4>
    <p class="text-muted">Masukkan 6 digit kode yang telah kami kirimkan ke email Anda.</p>

    <form method="POST">

        <div class="d-flex justify-content-between mb-3 mt-4" style="gap:10px;">
            <input type="text" name="kode1" maxlength="1" class="form-control kode" required>
            <input type="text" name="kode2" maxlength="1" class="form-control kode" required>
            <input type="text" name="kode3" maxlength="1" class="form-control kode" required>
            <input type="text" name="kode4" maxlength="1" class="form-control kode" required>
            <input type="text" name="kode5" maxlength="1" class="form-control kode" required>
            <input type="text" name="kode6" maxlength="1" class="form-control kode" required>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <button type="submit" name="verifikasi" class="btn btn-primary w-100">
            Verifikasi →
        </button>

        <!-- 🔁 RESEND + TIMER -->
        <div class="text-center mt-3">
            <a href="?email=<?= $email ?>&resend=1" id="resendBtn" class="text-decoration-none text-muted">
                Kirim ulang kode
            </a>
            <span id="timer" class="ms-2 text-muted small"></span>
        </div>

        <div class="text-center mt-2">
            <a href="login.php" class="text-muted text-decoration-none">← Kembali ke Login</a>
        </div>

    </form>

</div>
</div>
</div>

<!-- FOOTER -->
<div class="footer text-center mt-4 mb-3">
© 2024 UNIT LAYANAN PERMOHONAN REKOMENDASI DPRD PROVINSI JAWA TIMUR
</div>

<!-- JS OTP -->
<script>
const inputs = document.querySelectorAll('.kode');

inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === "Backspace" && input.value === '' && index > 0) {
            inputs[index - 1].focus();
        }
    });
});

/* 🔥 TIMER RESEND */
let delay = <?= $_SESSION['delay'] ?? 15 ?>;

const btn = document.getElementById("resendBtn");
const timer = document.getElementById("timer");

btn.style.pointerEvents = "none";
btn.style.opacity = "0.5";

let waktu = delay;

const countdown = setInterval(() => {
    timer.innerHTML = `(${waktu}s)`;

    waktu--;

    if (waktu < 0) {
        clearInterval(countdown);
        timer.innerHTML = "";
        btn.style.pointerEvents = "auto";
        btn.style.opacity = "1";
    }
}, 1000);
</script>

</body>
</html>
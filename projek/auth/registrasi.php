<?php
ob_start(); 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../config/koneksi.php';

require '../assets/phpmailer/Exception.php';
require '../assets/phpmailer/PHPMailer.php';
require '../assets/phpmailer/SMTP.php';

$errors = [];

$nama = $_POST['nama'] ?? '';
$nip = $_POST['nip'] ?? '';
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$jenis = $_POST['jenis'] ?? '';
$id_instansi = $_POST['id_instansi'] ?? '';



if (isset($_POST['register'])) {



    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    if ($password != $konfirmasi) {
        $errors['password'] = "Password tidak sama!";
    }

    if (empty($id_instansi)) {
        $errors['instansi'] = "Instansi wajib dipilih!";
    }

    $cek = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $errors['email'] = "Email sudah digunakan!";
    }

    $cek2 = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek2) > 0) {
        $errors['username'] = "Username sudah digunakan!";
    }
    if (!isset($_POST['setuju'])) {
    $errors['setuju'] = "Anda harus menyetujui syarat dan ketentuan!";
    }

    if (empty($errors)) {
    
        // 1️⃣ BUAT KODE
        $kode = rand(100000, 999999);

        // 2️⃣ SIMPAN KE DATABASE
        $_SESSION['register_temp'] = [
            'nama' => $nama,
            'nip' => $nip,
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'id_instansi' => $id_instansi,
            'kode' => $kode
        ];
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
        $mail->addAddress($email, $nama);

        $mail->isHTML(true);
        $mail->Subject = 'Kode Verifikasi Akun';
        $mail->Body = "<h2>Kode Verifikasi: $kode</h2>";

        $mail->send();

        // 🔥 redirect ke verifikasi

    echo "<script>window.location.replace('verifikasi.php?email=$email');</script>";
    exit;

    } 
        catch (Exception $e) {
            echo "Gagal kirim email: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Registrasi</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/css/registrasi.css">

<style>
.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}
</style>

</head>

<body>

<!-- NAVBAR -->
<div class="topbar d-flex justify-content-between align-items-center px-4 py-3">
    <div class="d-flex align-items-center gap-2">
        <div class="logo-box"></div>
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

<div class="wrapper">
<div class="main-card row g-0">

<!-- LEFT -->
<div class="col-12 col-md-5 left d-flex flex-column justify-content-between">
    <div>
        <h5>PENDAFTARAN AKUN</h5>
        <h1>Mulai Langkah Strategis Anda.</h1>
        <p>Bergabunglah dengan sistem layanan rekomendasi DPRD Jawa Timur</p>
    </div>
    <small>🔒 Sistem Keamanan Terenkripsi</small>
</div>

<!-- RIGHT -->
<div class="col-12 col-md-7 right">

<h3>Buat Akun Baru</h3>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?>
            <div>⚠️ <?= $err; ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST">

<!-- NAMA & NIP -->
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control"
        value="<?= htmlspecialchars($nama) ?>"
        placeholder="Masukkan nama lengkap" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">NIP</label>
        <input type="text" name="nip" class="form-control"
        value="<?= htmlspecialchars($nip) ?>"
        placeholder="Contoh: 1987654321" required>
    </div>
</div>

<!-- EMAIL & USERNAME -->
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Email Instansi</label>
        <input type="email" name="email" class="form-control"
        value="<?= isset($errors['username']) ? '' : htmlspecialchars($email) ?>"
        placeholder="contoh@email.com" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control"
        value="<?= isset($errors['username']) ? '' : htmlspecialchars($username) ?>"
        placeholder="Masukkan username" required>
    </div>
</div>

<!-- JENIS INSTANSI -->
<div class="mb-3">
    <label class="form-label">Jenis Instansi</label>
    <select id="jenis" name="jenis" class="form-control">
        <option value="">-- Pilih Jenis Instansi --</option>
        <option value="Dewan Perwakilan Rakyat Daerah"
        <?= (($_POST['jenis'] ?? '') == 'Dewan Perwakilan Rakyat Daerah') ? 'selected' : '' ?>>
        DPRD
        </option>
        <option value="Universitas"
        <?= ($jenis == 'Universitas') ? 'selected' : '' ?>>
        Universitas
        </option>
    </select>
</div>

<!-- INSTANSI -->
<div class="mb-3">
    <label class="form-label">Nama Instansi</label>
    <select name="id_instansi" id="instansi" class="form-control" required>
        <option value="">-- Pilih Instansi --</option>
    </select>
</div>

<!-- PASSWORD -->
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Password</label>
        <div class="input-wrapper">
        <input type="password" id="password" name="password"
        class="form-control input-password" placeholder="Minimal 6 karakter" required>

        <i class="bi bi-eye-slash toggle-password"
           onclick="togglePassword('password', this)"></i>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Konfirmasi Password</label>
        <div class="input-wrapper">
        <input type="password" id="konfirmasi" name="konfirmasi"
        class="form-control input-password" placeholder="Ulangi password" required>

        <i class="bi bi-eye-slash toggle-password"
           onclick="togglePassword('konfirmasi', this)"></i>
        </div>
    </div>
</div>

<!-- Checkbox untuk setuju syarat dan ketentuan -->
<div class="form-check d-flex align-items-center gap-2 mb-3">

    <input class="form-check-input mt-0" 
           type="checkbox" 
           id="setuju" 
           name="setuju"
           <?= isset($_POST['setuju']) ? 'checked' : '' ?>>

    <label class="form-check-label mb-0" for="setuju">
        Saya menyetujui 
        <a href="#">Syarat dan Ketentuan</a> serta 
        <a href="#">Kebijakan Privasi</a> yang berlaku.
    </label>

</div>
<?php if (isset($errors['setuju'])): ?>
    <small class="text-danger d-block mb-3">
        <?= $errors['setuju']; ?>
    </small>
<?php endif; ?>

<button type="submit" name="register" class="btn btn-primary w-100">
    Daftar Sekarang →
</button>

</form>
</div>
</div>
</div>

<div class="footer text-center mt-2 mb-3">
© 2026 DPRD JAWA TIMUR
</div>

<!-- 🔥 SCRIPT -->
<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);

    if (input.type === "password") {
        input.type = "text";
        el.classList.replace("bi-eye-slash", "bi-eye");
    } else {
        input.type = "password";
        el.classList.replace("bi-eye", "bi-eye-slash");
    }
}

// 🔥 FILTER INSTANSI
document.getElementById("jenis").addEventListener("change", function() {

    let jenis = this.value;

    fetch("get_instansi.php?jenis=" + jenis)
    .then(res => res.text())
    .then(data => {
        document.getElementById("instansi").innerHTML = data;
    });

});
</script>

</body>
</html>
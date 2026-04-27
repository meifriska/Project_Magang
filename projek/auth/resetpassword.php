<?php
include '../config/koneksi.php';

$email = mysqli_real_escape_string($conn, $_GET['email'] ?? '');
$error = '';

if (isset($_POST['reset'])) {

    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    if ($password != $konfirmasi) {
        $error = "Konfirmasi password tidak sama!";
    } else {

        mysqli_query($conn, "
            UPDATE user 
            SET pass='$password', kode_verifikasi=NULL
            WHERE email='$email'
        ");

        echo "<script>
            alert('Password berhasil diubah!');
            window.location='login.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ICON -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<link rel="stylesheet" href="../assets/css/login.css">

</head>

<body>

<div class="wrapper">
<div class="main-card row g-0">

<!-- LEFT -->
<div class="col-md-6 left d-flex flex-column justify-content-between">
    <div>
        <h5>KEAMANAN AKUN</h5>
        <h1>Pemulihan Akses Layanan.</h1>
        <p>Kami membantu Anda memulihkan akses ke sistem informasi secara aman dan cepat.</p>
    </div>

    <small>ℹ️ Jika Anda tidak memiliki akses email, hubungi admin.</small>
</div>

<!-- RIGHT -->
<div class="col-md-6 right">

    <h3>Buat Kata Sandi Baru</h3>
    <p class="text-muted">Masukkan kata sandi baru Anda untuk mengamankan akun.</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <!-- PASSWORD -->
        <div class="mb-3">
            <label>Kata Sandi Baru</label>
            <div class="position-relative">
                <input type="password" id="password" name="password" class="form-control pe-5" required>

                <i class="bi bi-eye-slash position-absolute toggle-pass"
                   data-target="password"
                   style="top:50%; right:15px; transform:translateY(-50%); cursor:pointer;">
                </i>
            </div>
        </div>

        <!-- KONFIRMASI -->
        <div class="mb-3">
            <label>Konfirmasi Kata Sandi Baru</label>
            <div class="position-relative">
                <input type="password" id="konfirmasi" name="konfirmasi" class="form-control pe-5" required>

                <i class="bi bi-eye-slash position-absolute toggle-pass"
                   data-target="konfirmasi"
                   style="top:50%; right:15px; transform:translateY(-50%); cursor:pointer;">
                </i>
            </div>
        </div>

        <!-- INFO -->
        <div class="alert alert-light">
            <strong>Persyaratan Kata Sandi:</strong>
            <ul class="mb-0">
                <li>Minimal 8 karakter</li>
                <li>Kombinasi huruf besar, kecil, dan angka</li>
            </ul>
        </div>

        <button type="submit" name="reset" class="btn btn-primary w-100">
            Simpan Kata Sandi →
        </button>

        <div class="text-center mt-3">
            <a href="login.php" class="text-muted text-decoration-none">← Kembali ke Login</a>
        </div>

    </form>

</div>
</div>
</div>

<div class="footer text-center mt-3">
© 2024 UNIT LAYANAN PERMOHONAN REKOMENDASI DPRD PROVINSI JAWA TIMUR
</div>

<!-- 🔥 SCRIPT ICON MATA -->
<script>
document.querySelectorAll('.toggle-pass').forEach(icon => {
    icon.addEventListener('click', function () {
        const target = document.getElementById(this.dataset.target);
        const type = target.type === "password" ? "text" : "password";
        target.type = type;

        this.classList.toggle("bi-eye");
        this.classList.toggle("bi-eye-slash");
    });
});
</script>

</body>
</html>
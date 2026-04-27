<?php
include '../config/koneksi.php';

$email = mysqli_real_escape_string($conn, $_GET['email'] ?? '');
$error = '';

if (isset($_POST['verifikasi'])) {

    $kode = $_POST['kode1'] . $_POST['kode2'] . $_POST['kode3'] . $_POST['kode4'] . $_POST['kode5'] . $_POST['kode6'];
    $kode = mysqli_real_escape_string($conn, $kode);

    $query = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {

        if ($kode == $user['kode_verifikasi']) {

            // 🔥 lanjut ke reset password
            header("Location: resetpassword.php?email=$email");
            exit;

        } else {
            $error = "Kode verifikasi salah!";
        }

    } else {
        $error = "User tidak ditemukan!";
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

<!-- 🔥 NAVBAR -->
<div class="topbar d-flex justify-content-between align-items-center px-4 py-3">
    <div class="d-flex align-items-center gap-2">
        <div style="width:35px;height:35px;background:#4b2aad;border-radius:8px;"></div>
        <div>
            <strong>ULPR DPRD JATIM</strong><br>
            <small>Layanan Rekomendasi</small>
        </div>
    </div>

    <div>
        <a href="login.php" class="btn btn-light btn-sm rounded-pill px-3">Masuk</a>
    </div>
</div>

<!-- 🔥 CONTENT -->
<div class="wrapper">
<div class="main-card row g-0">

<!-- LEFT -->
<div class="col-md-5 left d-flex flex-column justify-content-between">
    <div>
        <h5>KEAMANAN AKUN</h5>
        <h1>Pemulihan Akses Layanan.</h1>
        <p>Kami membantu Anda memulihkan akses ke sistem secara aman dan cepat.</p>
    </div>
    <small>ℹ️ Hubungi admin jika mengalami kendala.</small>
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

        <div class="text-center mt-3">
            <a href="#" class="text-decoration-none">Kirim ulang kode</a>
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
</script>

</body>
</html>
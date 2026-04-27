<?php
session_start();
include '../config/koneksi.php';

$error = '';

if (isset($_POST['login'])) {

    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    // =====================
    // 🔥 CEK ADMIN
    // =====================
    $queryAdmin = mysqli_query($conn, "
        SELECT * FROM admin
        WHERE email_admin='$user'
    ");

    if (mysqli_num_rows($queryAdmin) > 0) {

        $data = mysqli_fetch_assoc($queryAdmin);

        if ($pass == $data['password_admin']) {

            // 🔥 RESET SESSION (FIX BUG)
            session_unset();
            session_destroy();
            session_start();

            $_SESSION['login'] = true;
            $_SESSION['role'] = 'admin';
            $_SESSION['id_admin'] = $data['id_admin'];
            $_SESSION['nama'] = $data['nama_admin'];

            header("Location: ../admin/index.php");
            exit;

        } else {
            $error = "Password admin salah!";
        }

    } else {

        // =====================
        // 🔥 CEK USER
        // =====================
        $queryUser = mysqli_query($conn, "
            SELECT * FROM user
            WHERE email='$user' OR username='$user'
        ");

        if (mysqli_num_rows($queryUser) > 0) {

            $data = mysqli_fetch_assoc($queryUser);

                    if (password_verify($pass, $data['pass'])) {

                    // ✅ LOGIN SEMUA STATUS (aktif / nonaktif / pending)
                    session_unset();
                    session_destroy();
                    session_start();

                    $_SESSION['login'] = true;
                    $_SESSION['role'] = 'user';
                    $_SESSION['id_user'] = $data['id_user'];
                    $_SESSION['nama'] = $data['nama_lengkap'];
                    $_SESSION['status'] = $data['status_akun']; // 🔥 penting

                    header("Location: ../user/index.php");
                    exit;

                        } else {
                            $error = "Password salah!";
                        }

                    } else {
                        $error = "User tidak ditemukan!";
                    }
                }
            }
            ?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- 🔥 ICON -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<link rel="stylesheet" href="../assets/css/login.css">

</head>

<body>

<div class="wrapper">
<div class="main-card row g-0">

<!-- LEFT -->
<div class="col-md-6 left d-flex flex-column justify-content-between">
    <div>
        <h5>DPRD JAWA TIMUR</h5>
        <h1>E-Rekomendasi Layanan Publik</h1>
        <p>Sistem informasi permohonan rekomendasi terpadu untuk percepatan pembangunan dan kesejahteraan masyarakat Jawa Timur.</p>
    </div>

    <small>✔ Melayani lebih dari 1000+ permohonan setiap bulan secara transparan.</small>
</div>

<!-- RIGHT -->
<div class="col-md-6 right">

    <h3>Selamat Datang Kembali</h3>
    <p class="text-muted">Silakan masuk menggunakan akun resmi Anda</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label>Username / Email</label>
            <input type="text" name="user" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Kata Sandi</label>

            <div class="position-relative">
                <input type="password" id="password" name="password" class="form-control pe-5" required>

                <i class="bi bi-eye-slash position-absolute"
                   id="togglePassword"
                   style="top:50%; right:15px; transform:translateY(-50%); cursor:pointer;">
                </i>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <div>
                <input type="checkbox"> Ingat saya
            </div>
            <a href="lupakatasandi.php" class="text-decoration-none">Lupa Kata Sandi?</a>
        </div>

        <button type="submit" name="login" class="btn btn-primary w-100">
            Masuk ke Akun →
        </button>

        <div class="text-center mt-3">
            Belum memiliki akun?
            <a href="registrasi.php">Daftar Sekarang</a>
        </div>

    </form>

</div>
</div>
</div>

<div class="footer text-center mt-3">
© 2024 Unit Layanan Permohonan Rekomendasi DPRD Jawa Timur
</div>

<!-- 🔥 SCRIPT ICON MATA -->
<script>
const toggle = document.getElementById("togglePassword");
const password = document.getElementById("password");

toggle.addEventListener("click", function () {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);

    this.classList.toggle("bi-eye");
    this.classList.toggle("bi-eye-slash");
});
</script>

</body>
</html>
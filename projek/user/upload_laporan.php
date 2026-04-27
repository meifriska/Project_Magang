<?php
session_start();
include '../config/koneksi.php';

$nama = $_SESSION['nama'] ?? 'User';
$id_user = $_SESSION['id_user'] ?? 0;

$id = $_GET['id'] ?? 0;

// 🔥 proses upload
if (isset($_POST['upload'])) {

    if (!empty($_FILES['laporan']['name'])) {

        $nama_file = time().'_'.$_FILES['laporan']['name'];

        move_uploaded_file(
            $_FILES['laporan']['tmp_name'],
            "../uploads/".$nama_file
        );

        // simpan ke dokumen_permohonan
        mysqli_query($conn, "
            UPDATE dokumen_permohonan
            SET laporan_kegiatan='$nama_file'
            WHERE id_permohonan='$id'
        ");

        echo "<script>
            alert('Laporan berhasil diupload!');
            window.location='riwayat.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Laporan</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/dashboard_user.css">
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            <strong>DPRD JATIM</strong><br>
            <small>UNIT LAYANAN</small>
        </div>

        <ul class="menu">
            <li><a href="index.php">🏠 Beranda</a></li>
            <li><a href="laporan/laporan.php">📤 Laporan Kegiatan</a></li>
            <li><a href="riwayat.php">🕘 Riwayat</a></li>
            <li>
                <a href="chat.php">💬 Chat Admin</a>
            </li>
        </ul>
    </div>

    <!-- TOPBAR -->
    <div class="topbar mb-3">
        <div>
            <h5>Selamat datang, <strong><?= $nama ?></strong></h5>
            <small><?= date('l, d F Y') ?></small>
        </div>

        <div class="user-box position-relative" id="userBox">
            <span><?= $nama ?></span>
            <div class="avatar-icon" id="avatarBtn">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="dropdown-user" id="dropdownUser">
                <a href="#" onclick="confirmLogout()">Logout</a>
            </div>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="content p-4">

            <div class="hero mb-3">
                <h4>📤 Upload Laporan Kegiatan</h4>
                <small>Upload laporan maksimal 12 hari setelah kegiatan</small>
            </div>

            <div class="card">
                <div class="card-body">

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label">File Laporan</label>
                            <input type="file" name="laporan" class="form-control" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="upload" class="btn btn-success">
                                Upload
                            </button>

                            <a href="riwayat.php" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- SCRIPT -->
<script>
const avatar = document.getElementById("avatarBtn");
const dropdown = document.getElementById("dropdownUser");

avatar.addEventListener("click", function() {
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", function(e) {
    if (!document.getElementById("userBox").contains(e.target)) {
        dropdown.style.display = "none";
    }
});

function confirmLogout() {
    Swal.fire({
        title: 'Logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../auth/logout.php';
        }
    });
}
</script>

</body>
</html>
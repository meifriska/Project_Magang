<?php
session_start();
include '../config/koneksi.php';

// ambil session
$nama = $_SESSION['nama'] ?? 'User';
$id_user = $_SESSION['id_user'] ?? 0;

// ambil data disetujui
$q = mysqli_query($conn, "
    SELECT p.*, d.laporan_kegiatan
    FROM permohonan p
    LEFT JOIN dokumen_permohonan d 
    ON p.id_permohonan = d.id_permohonan
    WHERE p.id_user='$id_user'
    AND p.status_permohonan='disetujui'
    ORDER BY p.id_permohonan DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Kegiatan</title>

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
            <li class="active"><a href="#">🕘 Riwayat</a></li>
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
                <h4>🕘 Riwayat Kegiatan</h4>
                <small>Kegiatan yang sudah disetujui</small>
            </div>

            <div class="card">
                <div class="card-body">

                    <?php if(mysqli_num_rows($q) == 0): ?>
                        <div class="alert alert-info">
                            Belum ada kegiatan yang disetujui.
                        </div>
                    <?php endif; ?>

                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php
                        $no = 1;
                        while($d = mysqli_fetch_assoc($q)) {

                            $deadline = date('Y-m-d', strtotime($d['tanggal_selesai'].' +12 days'));
                            $hari_ini = date('Y-m-d');

                            if (!empty($d['laporan_kegiatan'])) {
                                $status = "<span class='badge bg-success'>Sudah Upload</span>";
                            } elseif ($hari_ini > $deadline) {
                                $status = "<span class='badge bg-danger'>Terlambat</span>";
                            } else {
                                $status = "<span class='badge bg-warning text-dark'>Belum Upload</span>";
                            }
                        ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td><?= $d['judul_tema'] ?></td>

                                <td><?= $d['penyelenggara'] ?></td>

                                <td>
                                    <?= $d['tanggal_mulai'] ?> - <?= $d['tanggal_selesai'] ?>
                                </td>

                                <td>
                                    <?= date('d M Y', strtotime($deadline)) ?>
                                </td>

                                <td><?= $status ?></td>

                                <td>
                                <?php if (empty($d['laporan_kegiatan'])) { ?>
                                    <a href="upload_laporan.php?id=<?= $d['id_permohonan'] ?>" 
                                    class="btn btn-primary btn-sm">
                                    Upload
                                    </a>
                                <?php } else { ?>
                                    <a href="../uploads/<?= $d['laporan_kegiatan'] ?>" 
                                    target="_blank"
                                    class="btn btn-success btn-sm">
                                    Lihat
                                    </a>
                                <?php } ?>
                                </td>
                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- 🔥 SCRIPT DROPDOWN -->
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
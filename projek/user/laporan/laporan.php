<?php
session_start();
include '../../config/koneksi.php';

// ambil nama user dari session
$nama = $_SESSION['nama'] ?? 'User';
$id_user = $_SESSION['id_user'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kegiatan</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/dashboard_user.css">

    <!-- 🔥 FIX TABLE BIAR RAPI -->
    <style>
        .table td {
            max-width: 160px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table td.catatan {
            max-width: 250px;
        }
    </style>
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
            <li><a href="../index.php">🏠 Beranda</a></li>
            <li class="active"><a href="laporan.php">📤 Laporan Kegiatan</a></li>
            <li><a href="../riwayat.php">🕘 Riwayat</a></li>
            <li>
                <a href="../chat.php">💬 Chat Admin</a>
            </li>
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
                <h4>📊 Laporan Kegiatan</h4>
                <small>Data permohonan yang kamu ajukan</small>
            </div>

            <div class="card">
                <div class="card-body">

                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Pemda</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>Tempat</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;

                            $q = mysqli_query($conn, "
                                SELECT * FROM permohonan
                                WHERE id_user='$id_user'
                                ORDER BY id_permohonan DESC
                            ");

                            while($d = mysqli_fetch_assoc($q)) {
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>

                                <td title="<?= $d['judul_tema'] ?>">
                                    <?= $d['judul_tema'] ?>
                                </td>

                                <td><?= $d['pemda'] ?: '-' ?></td>
                                <td><?= $d['penyelenggara'] ?: '-' ?></td>

                                <td>
                                    <?= $d['tanggal_mulai'] ?> - <?= $d['tanggal_selesai'] ?>
                                </td>

                                <td><?= $d['tempat_pelaksanaan'] ?></td>

                                <!-- STATUS -->
                                <td>
                                    <?php if($d['status_permohonan']=='pending'){ ?>
                                        <span class="badge bg-warning text-dark">Pending</span>

                                    <?php } elseif($d['status_permohonan']=='disetujui'){ ?>
                                        <span class="badge bg-success">Disetujui</span>

                                    <?php } else { ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php } ?>
                                </td>

                                <!-- CATATAN -->
                                <td class="catatan" title="<?= $d['catatan'] ?>">
                                    <?php if(!empty($d['catatan'])){ ?>
                                        <span class="text-danger">
                                            📝 <?= $d['catatan'] ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="text-muted">-</span>
                                    <?php } ?>
                                </td>

                                <!-- 🔥 AKSI (FIX FINAL) -->
                                <td>

                                    <?php if($d['status_permohonan']=='ditolak'){ ?>

                                        <!-- ✏️ EDIT SAJA -->
                                        <a href="../edit_permohonan.php?id=<?= $d['id_permohonan'] ?>"
                                           class="btn btn-warning btn-sm">
                                           ✏️
                                        </a>

                                    <?php } else { ?>
                                        <span class="text-muted">-</span>
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
            window.location.href = '../../auth/logout.php';
        }
    });
}
</script>

</body>
</html>
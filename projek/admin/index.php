<?php
session_start();
include '../config/koneksi.php';

// 🔒 proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>

    <!-- 🔥 ICON CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/dashboard_user.css">
</head>

<body>

<div class="wrapper">

    <!-- 🔥 SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            <strong>ADMIN PANEL</strong><br>
            <small>DPRD JATIM</small>
        </div>

        <ul class="menu">
            <li class="active">
                <a href="index.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li>
                <a href="permohonan.php">
                    <i class="bi bi-file-earmark-text"></i> Data Permohonan
                </a>
            </li>

            <li>
                <a href="instansi.php">
                    <i class="bi bi-building"></i> Data Instansi
                </a>
            </li>

            <li>
                <a href="jenis_kegiatan.php">
                    <i class="bi bi-list-check"></i> Jenis Kegiatan
                </a>
            </li>

            <li>
                <a href="user.php">
                    <i class="bi bi-people"></i> Data Pengguna
                </a>
            </li>

            <li>
                <a href="syarat.php">
                    <i class="bi bi-file-earmark-text"></i> Tambah Syarat Permohonan
                </a>
            </li>
            <li>
                <a href="laporan.php">
                    <i class="bi bi-clipboard-data"></i> Laporan
                </a>
            <li>
                <a href="chat.php">
                    <i class="bi bi-chat-dots"></i> Chat
                </a>
            </li>

        </ul>
    </div>

    <!-- MAIN -->
    <div class="main">

        <!-- 🔥 TOPBAR -->
        <div class="topbar">
            <div>
                <h5>Halo, <strong><?= $nama ?></strong></h5>
                <small><?= date('l, d F Y') ?></small>
            </div>

            <div class="user-box">
                <span><?= $nama ?></span>
                <div class="avatar-icon">
                    <i class="bi bi-person"></i>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">

            <!-- HERO -->
            <div class="hero mb-3">
                <h4><i class="bi bi-speedometer2"></i> Dashboard Admin</h4>
                <small>Kelola dan pantau semua permohonan masuk</small>
            </div>

            <!-- STATISTIK -->
            <div class="row mb-3">

                <?php
                $total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM permohonan"));
                $pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM permohonan WHERE status_permohonan='pending'"));
                $disetujui = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM permohonan WHERE status_permohonan='disetujui'"));
                $ditolak = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM permohonan WHERE status_permohonan='ditolak'"));
                ?>

                <div class="col-md-3">
                    <div class="card p-3">
                        <h6>Total</h6>
                        <h4><?= $total ?></h4>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3">
                        <h6>Pending</h6>
                        <h4><?= $pending ?></h4>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3">
                        <h6>Disetujui</h6>
                        <h4><?= $disetujui ?></h4>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3">
                        <h6>Ditolak</h6>
                        <h4><?= $ditolak ?></h4>
                    </div>
                </div>

            </div>

            <!-- TABEL -->
            <div class="card">
                <div class="card-body">

                    <h6 class="mb-3">Data Permohonan Terbaru</h6>

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Pemda</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;
                            $q = mysqli_query($conn, "SELECT * FROM permohonan ORDER BY id_permohonan DESC LIMIT 5");

                            while($d = mysqli_fetch_assoc($q)) {
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $d['judul_tema'] ?></td>
                                <td><?= $d['pemda'] ?></td>
                                <td><?= $d['tanggal_mulai'] ?></td>
                                <td>
                                    <?php if($d['status_permohonan']=='pending'){ ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php } elseif($d['status_permohonan']=='disetujui'){ ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger">Ditolak</span>
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

</body>
</html>
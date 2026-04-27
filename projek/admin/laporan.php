<?php
session_start();
include '../config/koneksi.php';

// 🔒 proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$nama = $_SESSION['nama'];

// 🔥 ambil data + laporan
$q = mysqli_query($conn, "
SELECT p.*, d.laporan_kegiatan
FROM permohonan p
LEFT JOIN dokumen_permohonan d 
ON p.id_permohonan = d.id_permohonan
WHERE p.status_permohonan = 'disetujui'
ORDER BY p.id_permohonan DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Laporan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard_user.css">
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR (SAMA PERSIS) -->
    <div class="sidebar">
        <div class="logo">
            <strong>ADMIN PANEL</strong><br>
            <small>DPRD JATIM</small>
        </div>

        <ul class="menu">
            <li>
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
                    <i class="bi bi-file-earmark-text"></i> Tambah Syarat
                </a>
            </li>

            <li class="active">
                <a href="laporan.php">
                    <i class="bi bi-clipboard-data"></i> Laporan
                </a>
            </li>

            <li>
                <a href="chat.php">
                    <i class="bi bi-chat-dots"></i> Chat
                </a>
            </li>
        </ul>
    </div>

    <!-- MAIN -->
    <div class="main">

        <!-- TOPBAR (SAMA PERSIS) -->
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

            <div class="hero mb-3">
                <h4><i class="bi bi-clipboard-data"></i> Data Laporan</h4>
                <small>Monitoring laporan kegiatan user</small>
            </div>

            <div class="card">
                <div class="card-body">

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Laporan</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php 
                        $no = 1;
                        while($d = mysqli_fetch_assoc($q)) { 
                        ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td><?= $d['judul_tema'] ?></td>

                                <td><?= $d['penyelenggara'] ?></td>

                                <td>
                                    <?= $d['tanggal_mulai'] ?> - <?= $d['tanggal_selesai'] ?>
                                </td>

                                <!-- STATUS -->
                                <td>
                                <?php if ($d['status_permohonan'] != 'disetujui' || empty($d['laporan_kegiatan'])) { ?>

                                    <span class="badge bg-danger">Belum Upload</span>

                                <?php } else { ?>

                                    <span class="badge bg-success">Sudah Upload</span>

                                <?php } ?>
                                </td>

                                <!-- 🔥 LAPORAN -->
                                <td>
                                <?php if (!empty($d['laporan_kegiatan'])) { ?>

                                    <a href="../uploads/<?= $d['laporan_kegiatan'] ?>" 
                                       target="_blank"
                                       class="btn btn-success btn-sm">
                                       <i class="bi bi-eye"></i>
                                    </a>

                                <?php } else { ?>

                                    <span class="text-muted">Belum ada</span>

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
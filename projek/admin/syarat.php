<?php
session_start();
include '../config/koneksi.php';

// 🔥 ambil nama admin dari session
$nama = $_SESSION['nama'] ?? 'Admin';

// =====================
// TAMBAH
// =====================
if (isset($_POST['tambah'])) {
    $nama_syarat = $_POST['nama'];
    $tipe = $_POST['tipe'];

    mysqli_query($conn, "
        INSERT INTO syarat_permohonan (nama_syarat, tipe)
        VALUES ('$nama_syarat', '$tipe')
    ");

    header("Location: syarat.php");
    exit;
}

// =====================
// HAPUS
// =====================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    mysqli_query($conn, "
        DELETE FROM syarat_permohonan 
        WHERE id_syarat='$id'
    ");

    header("Location: syarat.php");
    exit;
}

// =====================
// DATA
// =====================
$data = mysqli_query($conn, "
    SELECT * FROM syarat_permohonan 
    ORDER BY id_syarat DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Syarat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard_user.css">
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            <strong>ADMIN PANEL</strong><br>
            <small>DPRD JATIM</small>
        </div>

        <ul class="menu">
            <li><a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="permohonan.php"><i class="bi bi-file-earmark-text"></i> Data Permohonan</a></li>
            <li><a href="instansi.php"><i class="bi bi-building"></i> Data Instansi</a></li>
            <li><a href="jenis_kegiatan.php"><i class="bi bi-list-check"></i> Jenis Kegiatan</a></li>
            <li><a href="user.php"><i class="bi bi-people"></i> Data Pengguna</a></li>
            <li class="active"><a href="syarat.php"><i class="bi bi-file-earmark-text"></i> Syarat Permohonan</a></li>
            <li>
                <a href="laporan.php">
                    <i class="bi bi-clipboard-data"></i> Laporan
                </a>
            </li>
            <li><a href="chat.php"><i class="bi bi-chat-dots"></i> Chat</a></li>
        </ul>
    </div>

    <!-- MAIN -->
    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div>
                <h5>Halo, <strong><?= $nama ?></strong></h5>
                <small><?= date('l, d F Y') ?></small>
            </div>

            <div class="user-box d-flex align-items-center gap-2">
                <span><?= $nama ?></span>
                <div class="avatar-icon">
                    <i class="bi bi-person"></i>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">

            <div class="hero mb-3">
                <h4><i class="bi bi-file-earmark-text"></i> Data Syarat</h4>
                <small>Kelola semua syarat permohonan</small>
            </div>

            <div class="card p-4 shadow-sm mb-4">
                <h5 class="mb-3">➕ Tambah Syarat</h5>

                <form method="POST" class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Syarat" required>
                    </div>

                    <div class="col-md-3">
                        <select name="tipe" class="form-control">
                            <option value="file">File Upload</option>
                            <option value="text">Text</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button name="tambah" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Tambah
                        </button>
                    </div>
                </form>
            </div>

            <!-- TABLE -->
            <div class="card p-4 shadow-sm">

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Syarat</th>
                            <th width="120">Tipe</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php 
                    $no = 1;
                    while($d = mysqli_fetch_assoc($data)) { 
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $d['nama_syarat'] ?></td>
                            <td>
                                <span class="badge bg-<?= $d['tipe']=='file' ? 'primary' : 'success' ?>">
                                    <?= $d['tipe'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="?hapus=<?= $d['id_syarat'] ?>" 
                                   onclick="return confirm('Yakin mau hapus?')"
                                   class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>

</body>
</html>
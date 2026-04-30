<?php
session_start();
include '../config/koneksi.php';


// 🔥 kalau tidak ada data → balik ke index
if (!isset($_SESSION['form'])) {
    header("Location: index.php");
    exit;
}



// ambil data dari session
$form = $_SESSION['form'] ?? [];
$uploaded = $_SESSION['uploaded'] ?? [];
$nama = $_SESSION['nama'] ?? 'User';

// 🔥 ambil nama kegiatan
$id_kegiatan = $form['id_jenis_kegiatan'] ?? 0;
$nama_kegiatan = '-';

if ($id_kegiatan) {
    $q = mysqli_query($conn, "SELECT nama_kegiatan FROM jenis_kegiatan WHERE id_jenis_kegiatan='$id_kegiatan'");
    $d = mysqli_fetch_assoc($q);
    $nama_kegiatan = $d['nama_kegiatan'] ?? '-';
}

// fungsi tampil file

function fileItem($label, $field) {
    global $uploaded;

    if (!empty($uploaded[$field])) {

        $filePath = "../uploads/" . $uploaded[$field];

        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= $label ?>
            <div>
                <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-info">
                    Lihat
                </a>

                <button type="button"
                        onclick="hapusFile('<?= $field ?>')"
                        class="btn btn-sm btn-danger">
                    Hapus
                </button>
            </div>
        </li>
        <?php

    } else {

        // 🔥 kalau sudah dihapus → tampil beda
        ?>
        <li class="list-group-item text-muted">
            <?= $label ?> : (Tidak ada file)
        </li>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Preview Data</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/Projek_magang/projek/assets/css/dashboard_user.css?v=2">

</head>

<body>

<div class="layout">

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <strong>DPRD JATIM</strong><br>
        <small>UNIT LAYANAN</small>
    </div>

    <ul class="menu">
        <li class="active">📄 Preview</li>

        <li>
            <a href="chat.php">💬 Chat Admin</a>
        </li>
    </ul>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="topbar">
        <div>
            <h5>Preview Data, <strong><?= $nama ?></strong></h5>
            <small>Pastikan semua data sudah benar sebelum submit</small>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
<!-- DATA PERMOHONAN -->
<div class="card form-card mb-4">
    <h6 class="mb-3">📄 Data Permohonan</h6>

    <div class="row g-3">

        <div class="col-md-6">
            <label>Jenis Layanan</label>
            <input type="text" class="form-control"
            value="<?= $form['jenis_layanan'] ?? '-' ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>Pemerintah Daerah</label>
            <input type="text" class="form-control"
            value="<?= $form['pemda'] ?? '-' ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>Jenis Kegiatan</label>
            <input type="text" class="form-control" value="<?= $nama_kegiatan ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>Judul Tema</label>
            <input type="text" class="form-control" value="<?= $form['judul'] ?? '' ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>Tanggal Pelaksanaan</label>
            <input type="text" class="form-control" readonly
            value="<?=
                (!empty($form['tanggal_mulai']) && !empty($form['tanggal_selesai']))
                ? date('d M Y', strtotime($form['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($form['tanggal_selesai']))
                : 'Belum diisi'
            ?>">
        </div>

        <div class="col-md-6">
            <label>Tempat</label>
            <input type="text" class="form-control" value="<?= $form['tempat'] ?? '' ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>Jumlah Peserta</label>
            <input type="text" class="form-control" value="<?= $form['peserta'] ?? '' ?>" readonly>
        </div>

        <div class="col-md-6">
            <label>Penyelenggara</label>
            <input type="text" class="form-control"
            value="<?= $form['penyelenggara'] ?? '-' ?>" readonly>
        </div>

        <?php
        $syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan WHERE tipe='text'");

        while($s = mysqli_fetch_assoc($syarat)) {
        $field = 'syarat_'.$s['id_syarat'];

        ?>
            <div class="col-md-6">
                <label><?= $s['nama_syarat'] ?></label>
                <input type="text" class="form-control"
                       value="<?= $form[$field] ?? '-' ?>" readonly>
            </div>
        <?php } ?>

    </div>
</div>
        

        <!-- DOKUMEN -->
        <div class="card form-card">

            <h6 class="mb-3">📎 Dokumen Upload</h6>

            <ul class="list-group">

    <?php
    fileItem('TOR', 'tor');
    fileItem('Surat BPSDM', 'surat_bpsdm');
    fileItem('Jadwal', 'jadwal');
    fileItem('Penawaran', 'penawaran');
    fileItem('MOU', 'mou');
    fileItem('Balasan', 'balasan');
    fileItem('Akreditasi', 'akreditasi');
    fileItem('Undangan', 'undangan');
    ?>

    <?php
    $syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan WHERE tipe='file'");

    while($s = mysqli_fetch_assoc($syarat)) {

        $field = 'syarat_'.$s['id_syarat'];
        $file = $uploaded[$field] ?? '';
    ?>

    <li class="list-group-item d-flex justify-content-between align-items-center">
        <?= $s['nama_syarat'] ?>

        <div>
            <?php if (!empty($file)) { ?>
                <a href="../uploads/<?= $file ?>" target="_blank" class="btn btn-sm btn-info">
                    Lihat
                </a>

                <button type="button"
                        onclick="hapusFile('<?= $field ?>')"
                        class="btn btn-sm btn-danger">
                    Hapus
                </button>
            <?php } else { ?>
                <span class="text-muted">Tidak ada file</span>
            <?php } ?>
        </div>
    </li>

    <?php } ?>

</ul>
            <!-- TOMBOL -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <?php
                $_SESSION['uploaded'] = $uploaded;
                ?>
                <a href="index.php" class="btn btn-warning">
                    ✏️ Edit
                </a>

                <form action="proses_simpan.php" method="POST">
                    <button type="submit" name="submit" class="btn btn-success">
                        ✅ Submit Final
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>

    </div>
</div>
</div>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                function hapusFile(field) {
                    Swal.fire({
                        title: 'Hapus dokumen?',
                        text: "Dokumen akan dihapus dari daftar!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: "#6a4df5",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "hapus.php?field=" + field;
                        }
                    });
                }
                </script>

</script>
</body>
</html>
<?php
session_start();
include '../config/koneksi.php';

// proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$nama_admin = $_SESSION['nama'] ?? 'Admin';

// =====================
// TAMBAH
// =====================
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    mysqli_query($conn, "
        INSERT INTO jenis_kegiatan (nama_kegiatan)
        VALUES ('$nama')
    ");

    header("Location: jenis_kegiatan.php");
    exit;
}

// =====================
// UPDATE
// =====================
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    mysqli_query($conn, "
        UPDATE jenis_kegiatan
        SET nama_kegiatan='$nama'
        WHERE id_jenis_kegiatan='$id'
    ");

    header("Location: jenis_kegiatan.php");
    exit;
}

// =====================
// HAPUS
// =====================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    mysqli_query($conn, "
        DELETE FROM jenis_kegiatan
        WHERE id_jenis_kegiatan='$id'
    ");

    header("Location: jenis_kegiatan.php");
    exit;
}

// =====================
// DATA
// =====================
$q = mysqli_query($conn, "SELECT * FROM jenis_kegiatan ORDER BY id_jenis_kegiatan DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jenis Kegiatan</title>

    <!-- ICON -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- STYLE -->
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

            <li class="active">
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
            <li >
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
        <div class="topbar">
            <div>
                <h5>Halo, <strong><?= $nama_admin ?></strong></h5>
                <small><?= date('l, d F Y') ?></small>
            </div>

            <div class="user-box">
                <span><?= $nama_admin ?></span>
                <div class="avatar-icon">
                    <i class="bi bi-person"></i>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">

            <div class="hero mb-3">
                <h4><i class="bi bi-list-check"></i> Jenis Kegiatan</h4>
                <small>Kelola jenis kegiatan</small>
            </div>

            <!-- TAMBAH -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-9">
                                <input type="text" name="nama" class="form-control"
                                       placeholder="Contoh: Seminar" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="tambah" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL -->
            <div class="card">
                <div class="card-body">

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Kegiatan</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;
                            while ($d = mysqli_fetch_assoc($q)) {
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $d['nama_kegiatan'] ?></td>
                                <td>

                                    <!-- EDIT -->
                                    <button class="btn btn-warning btn-sm"
                                        onclick="editData(
                                            <?= $d['id_jenis_kegiatan'] ?>,
                                            '<?= htmlspecialchars($d['nama_kegiatan']) ?>'
                                        )">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- HAPUS -->
                                    <a href="?hapus=<?= $d['id_jenis_kegiatan'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Hapus data ini?')">
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

</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-pencil"></i> Edit Jenis
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">

          <label>Nama Kegiatan</label>
          <input type="text" name="nama" id="edit_nama" class="form-control" required>
        </div>

        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function editData(id, nama) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;

    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>

</body>
</html>
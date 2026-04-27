<?php
session_start();
include '../config/koneksi.php';

// 🔒 proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$nama = $_SESSION['nama'] ?? 'Admin';

// =======================
// 🔥 TAMBAH ADMIN
// =======================
if (isset($_POST['tambah_admin'])) {
    $nama_admin = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    mysqli_query($conn, "
        INSERT INTO admin (nama_admin, email_admin, password_admin)
        VALUES ('$nama_admin', '$email', '$password')
    ");

    header("Location: user.php#adminTab"); // 🔥 balik ke tab admin
    exit;
}

// =======================
// 🔥 HAPUS USER
// =======================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM user WHERE id_user='$id'");
    header("Location: user.php#userTab");
    exit;
}

// =======================
// 🔥 HAPUS ADMIN
// =======================
if (isset($_GET['hapus_admin'])) {
    $id = $_GET['hapus_admin'];
    mysqli_query($conn, "DELETE FROM admin WHERE id_admin='$id'");
    header("Location: user.php#adminTab"); // 🔥 balik ke tab admin
    exit;
}

// =======================
// 🔥 DATA USER
// =======================
$user = mysqli_query($conn, "
    SELECT u.*, i.nama_instansi
    FROM user u
    LEFT JOIN instansi i ON u.id_instansi = i.id_instansi
    ORDER BY u.id_user DESC
");

// =======================
// 🔥 DATA ADMIN
// =======================
$admin = mysqli_query($conn, "SELECT * FROM admin ORDER BY id_admin DESC");

// =======================
// 🔥 UPDATE STATUS USER
// =======================
if (isset($_POST['update_status'])) {
    $id = $_POST['id_user'];
    $status = $_POST['status'];

    mysqli_query($conn, "
        UPDATE user SET status_akun='$status'
        WHERE id_user='$id'
    ");

    header("Location: user.php#userTab");
    exit;
}

// =======================
// 🔥 UPDATE ADMIN
// =======================
if (isset($_POST['update_admin'])) {
    $id = $_POST['id_admin'];
    $nama_admin = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    mysqli_query($conn, "
        UPDATE admin SET
        nama_admin='$nama_admin',
        email_admin='$email',
        password_admin='$password'
        WHERE id_admin='$id'
    ");

    header("Location: user.php#adminTab");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Pengguna</title>

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
        <li class="active"><a href="user.php"><i class="bi bi-people"></i> Data Pengguna</a></li>
        <li><a href="syarat.php"><i class="bi bi-file-earmark-text"></i> Tambah Syarat Permohonan</a></li>
        <li><a href="laporan.php"><i class="bi bi-clipboard-data"></i> Laporan</a></li>
        <li><a href="chat.php"><i class="bi bi-chat-dots"></i> Chat</a></li>
    </ul>
</div>

<!-- MAIN -->
<div class="main">

<!-- TOPBAR -->
<div class="topbar">
    <div>
        <h5>Halo, <strong><?= $nama ?></strong></h5>
        <small><?= date('l, d F Y') ?></small>
    </div>
</div>

<div class="content">

<!-- HERO -->
<div class="hero mb-3">
    <h4><i class="bi bi-people"></i> Manajemen Pengguna</h4>
    <small>Kelola user & admin/operator</small>
</div>

<!-- TABS -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#userTab">
            <i class="bi bi-person"></i> User
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#adminTab">
            <i class="bi bi-shield-lock"></i> Admin
        </button>
    </li>
</ul>

<div class="tab-content">

<!-- ================= USER ================= -->
<div class="tab-pane fade show active" id="userTab">

<div class="card shadow-sm">
<div class="card-body">

<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th>No</th>
<th>Nama</th>
<th>Email</th>
<th>Instansi</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($d = mysqli_fetch_assoc($user)) { ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $d['nama_lengkap'] ?></td>
<td><?= $d['email'] ?></td>
<td><?= $d['nama_instansi'] ?? '-' ?></td>
<td>
    <span class="badge <?= $d['status_akun']=='aktif' ? 'bg-success' : 'bg-warning text-dark' ?>">
        <?= $d['status_akun'] ?>
    </span>
</td>
<td>
    <form method="POST" style="display:inline;">
        <input type="hidden" name="id_user" value="<?= $d['id_user'] ?>">

        <?php if($d['status_akun']=='aktif'){ ?>
            <input type="hidden" name="status" value="tidak aktif">
            <button class="btn btn-warning btn-sm" name="update_status">
                <i class="bi bi-x-circle"></i>
            </button>
        <?php } else { ?>
            <input type="hidden" name="status" value="aktif">
            <button class="btn btn-success btn-sm" name="update_status">
                <i class="bi bi-check-circle"></i>
            </button>
        <?php } ?>
    </form>

    <a href="?hapus=<?= $d['id_user'] ?>" 
       class="btn btn-danger btn-sm"
       onclick="return confirm('Hapus user?')">
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

<!-- ================= ADMIN ================= -->
<div class="tab-pane fade" id="adminTab">

<!-- FORM TAMBAH ADMIN -->
<div class="card mb-3 shadow-sm">
<div class="card-body">

<form method="POST">
<div class="row g-2">

<div class="col-md-4">
    <input type="text" name="nama" class="form-control" placeholder="Nama Admin" required>
</div>

<div class="col-md-4">
    <input type="email" name="email" class="form-control" placeholder="Email Admin" required>
</div>

<div class="col-md-3">
    <input type="text" name="password" class="form-control" placeholder="Password" required>
</div>

<div class="col-md-1">
    <button name="tambah_admin" class="btn btn-primary w-100">
        <i class="bi bi-plus"></i>
    </button>
</div>

</div>
</form>

</div>
</div>

<!-- LIST ADMIN -->
<div class="card shadow-sm">
<div class="card-body">

<table class="table table-hover">
<thead class="table-light">
<tr>
<th>No</th>
<th>Nama</th>
<th>Email</th>
<th>Password</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($a = mysqli_fetch_assoc($admin)) { ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $a['nama_admin'] ?></td>
<td><?= $a['email_admin'] ?></td>
<td><span class="text-danger"><?= $a['password_admin'] ?></span></td>
<td>
    <button class="btn btn-warning btn-sm"
    onclick="editAdmin(
    <?= $a['id_admin'] ?>,
    '<?= $a['nama_admin'] ?>',
    '<?= $a['email_admin'] ?>',
    '<?= $a['password_admin'] ?>'
    )">
    <i class="bi bi-pencil"></i>
    </button>

    <a href="?hapus_admin=<?= $a['id_admin'] ?>"
    class="btn btn-danger btn-sm"
    onclick="return confirm('Hapus admin?')">
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
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function editAdmin(id, nama, email, password) {
    document.getElementById('edit_id_admin').value = id;
    document.getElementById('edit_nama_admin').value = nama;
    document.getElementById('edit_email_admin').value = email;
    document.getElementById('edit_password_admin').value = password;

    new bootstrap.Modal(document.getElementById('modalEditAdmin')).show();
}
</script>

<div class="modal fade" id="modalEditAdmin">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST">
<div class="modal-header">
<h5>Edit Admin</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="id_admin" id="edit_id_admin">

<input type="text" name="nama" id="edit_nama_admin" class="form-control mb-2" required>

<input type="email" name="email" id="edit_email_admin" class="form-control mb-2" required>

<input type="text" name="password" id="edit_password_admin" class="form-control" required>

</div>

<div class="modal-footer">
<button type="submit" name="update_admin" class="btn btn-primary">Simpan</button>
</div>

</form>

</div>
</div>
</div>

</body>
</html>
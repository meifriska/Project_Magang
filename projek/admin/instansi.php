<?php
session_start();
include '../config/koneksi.php';

// 🔒 proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$nama_admin = $_SESSION['nama'] ?? 'Admin';

// 🔥 DATA DAERAH
$daerah = [
    "Surabaya","Sidoarjo","Gresik","Malang","Kediri","Blitar","Madiun",
    "Jember","Banyuwangi","Tulungagung","Lamongan","Pasuruan","Probolinggo",
    "Mojokerto","Magetan","Ngawi","Ponorogo","Trenggalek","Bondowoso",
    "Situbondo","Pacitan","Lumajang","Nganjuk","Bojonegoro","Bangkalan",
    "Sampang","Pamekasan","Sumenep"
];

// TAMBAH
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    mysqli_query($conn, "
        INSERT INTO instansi (nama_instansi, jenis_instansi, alamat_instansi)
        VALUES ('$nama', '$jenis', '$alamat')
    ");

    header("Location: instansi.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    mysqli_query($conn, "
        UPDATE instansi SET
        nama_instansi='$nama',
        jenis_instansi='$jenis',
        alamat_instansi='$alamat'
        WHERE id_instansi='$id'
    ");

    header("Location: instansi.php");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM instansi WHERE id_instansi='$id'");
    header("Location: instansi.php");
    exit;
}

// SEARCH
$keyword = $_GET['cari'] ?? '';

$query = "SELECT * FROM instansi";
if (!empty($keyword)) {
    $query .= " WHERE nama_instansi LIKE '%$keyword%'
                OR jenis_instansi LIKE '%$keyword%'
                OR alamat_instansi LIKE '%$keyword%'";
}
$query .= " ORDER BY id_instansi DESC";

$q = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Instansi</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard_user.css">

<!-- 🔥 SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>

<div class="wrapper">

<div class="sidebar">
    <div class="logo">
        <strong>ADMIN PANEL</strong><br>
        <small>DPRD JATIM</small>
    </div>

    <ul class="menu">
        <li><a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="permohonan.php"><i class="bi bi-file-earmark-text"></i> Data Permohonan</a></li>
        <li class="active"><a href="instansi.php"><i class="bi bi-building"></i> Data Instansi</a></li>
        <li><a href="jenis_kegiatan.php"><i class="bi bi-list-check"></i> Jenis Kegiatan</a></li><li>
        <li><a href="user.php"><i class="bi bi-people"></i> Data Pengguna</a></li>
        <li><a href="syarat.php"><i class="bi bi-file-earmark-text"></i> Tambah Syarat Permohonan</a></li>
        <li >
                <a href="laporan.php">
                    <i class="bi bi-clipboard-data"></i> Laporan
                </a>
        </li>
        <li><a href="chat.php"><i class="bi bi-chat-dots"></i> Chat</a></li>
    </ul>
</div>

<div class="main">

<div class="topbar">
    <div>
        <h5>Halo, <strong><?= $nama_admin ?></strong></h5>
        <small><?= date('l, d F Y') ?></small>
    </div>
</div>

<div class="content">

<div class="hero mb-3">
    <h4><i class="bi bi-building"></i> Data Instansi</h4>
</div>

<!-- SEARCH -->
<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-9">
            <input type="text" name="cari" class="form-control"
                   placeholder="Cari instansi..."
                   value="<?= htmlspecialchars($keyword) ?>">
        </div>
        <div class="col-md-3">
            <button class="btn btn-dark w-100">
                <i class="bi bi-search"></i> Cari
            </button>
        </div>
    </div>
</form>

<!-- TAMBAH -->
<div class="card mb-3 shadow-sm">
<div class="card-body">
<form method="POST">
<div class="row g-2">

<div class="col-md-4">
    <input type="text" name="nama" class="form-control" placeholder="Nama Instansi" required>
</div>

<div class="col-md-3">
    <select name="jenis" class="form-control" required>
        <option value="">-- Jenis --</option>
        <option value="DPRD">DPRD</option>
        <option value="Universitas">Universitas</option>
    </select>
</div>

<div class="col-md-3">
    <select name="alamat" id="selectDaerah" class="form-control select2" required>
        <option value="">-- Daerah --</option>
        <?php foreach ($daerah as $d) { ?>
            <option value="<?= $d ?>"><?= $d ?></option>
        <?php } ?>
    </select>
</div>

<div class="col-md-2">
    <button type="submit" name="tambah" class="btn btn-primary w-100">
        <i class="bi bi-plus-circle"></i>
    </button>
</div>

</div>
</form>
</div>
</div>

<!-- TABEL -->
<div class="card shadow-sm">
<div class="card-body">

<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th>No</th>
<th>Nama</th>
<th>Jenis</th>
<th>Alamat</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while ($d = mysqli_fetch_assoc($q)) { ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $d['nama_instansi'] ?></td>
<td><?= $d['jenis_instansi'] ?></td>
<td><?= $d['alamat_instansi'] ?? '-' ?></td>
<td>

<button class="btn btn-warning btn-sm"
onclick="editData(
<?= $d['id_instansi'] ?>,
'<?= htmlspecialchars($d['nama_instansi']) ?>',
'<?= $d['jenis_instansi'] ?>',
'<?= $d['alamat_instansi'] ?>'
)">
<i class="bi bi-pencil"></i>
</button>

<button class="btn btn-danger btn-sm"
onclick="hapusData(<?= $d['id_instansi'] ?>)">
<i class="bi bi-trash"></i>
</button>

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
<h5>Edit Instansi</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="id" id="edit_id">

<input type="text" name="nama" id="edit_nama" class="form-control mb-2" required>

<select name="jenis" id="edit_jenis" class="form-control mb-2">
<option value="DPRD">DPRD</option>
<option value="Universitas">Universitas</option>
</select>

<select name="alamat" id="edit_alamat" class="form-control select2">
<?php foreach ($daerah as $d) { ?>
<option value="<?= $d ?>"><?= $d ?></option>
<?php } ?>
</select>

</div>

<div class="modal-footer">
<button type="submit" name="update" class="btn btn-primary">Simpan</button>
</div>
</form>

</div>
</div>
</div>

<!-- MODAL HAPUS -->
<div class="modal fade" id="modalHapus">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content text-center p-3">

<div class="modal-body">
<div class="mb-3 text-danger" style="font-size:45px;">
<i class="bi bi-exclamation-triangle-fill"></i>
</div>

<h5>Yakin mau hapus?</h5>
<p class="text-muted small">Data tidak bisa dikembalikan</p>

<div class="d-flex justify-content-center gap-2 mt-3">
<button class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
<a id="btnHapus" class="btn btn-danger px-4">Hapus</a>
</div>

</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- 🔥 SELECT2 -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function editData(id, nama, jenis, alamat) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_jenis').value = jenis;
    document.getElementById('edit_alamat').value = alamat;

    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function hapusData(id) {
    document.getElementById('btnHapus').href = "?hapus=" + id;
    new bootstrap.Modal(document.getElementById('modalHapus')).show();
}

// 🔥 AKTIFKAN SELECT2
$(document).ready(function() {

    $('#selectDaerah').select2({
        placeholder: "-- Daerah --",
        width: '100%'
    });

    $('#edit_alamat').select2({
        dropdownParent: $('#modalEdit'),
        width: '100%'
    });

});
</script>

</body>
</html>
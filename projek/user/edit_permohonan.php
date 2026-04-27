<?php
session_start();
include '../config/koneksi.php';

// proteksi user
if (!isset($_SESSION['id_user'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'];
$id_user = $_SESSION['id_user'];

// ambil data permohonan + dokumen default
$q = mysqli_query($conn, "
    SELECT p.*, d.*
    FROM permohonan p
    LEFT JOIN dokumen_permohonan d
    ON p.id_permohonan = d.id_permohonan
    WHERE p.id_permohonan='$id' AND p.id_user='$id_user'
");
$data = mysqli_fetch_assoc($q);

$uploaded = [
    'tor' => $data['tor'] ?? null,
    'surat_bpsdm' => $data['surat_bpsdm'] ?? null,
    'jadwal' => $data['jadwal_kegiatan'] ?? null,
    'penawaran' => $data['surat_penawaran'] ?? null,
    'mou' => $data['mou'] ?? null,
    'balasan' => $data['surat_balasan'] ?? null,
    'akreditasi' => $data['akreditasi'] ?? null,
    'undangan' => $data['undangan_pembukaan'] ?? null,
];

// =====================
// 🔥 UPDATE
// =====================
if (isset($_POST['update'])) {

    // ambil data lama
    $qLama = mysqli_query($conn, "
        SELECT * FROM dokumen_permohonan
        WHERE id_permohonan='$id'
    ");
    $dataLama = mysqli_fetch_assoc($qLama) ?? [];

    function uploadOrKeep($input, $lama) {
        if (!empty($_FILES[$input]['name']) && $_FILES[$input]['error'] == 0) {
            $nama = time() . '_' . $_FILES[$input]['name'];
            move_uploaded_file($_FILES[$input]['tmp_name'], "../uploads/" . $nama);
            return $nama;
        }
        return $lama;
    }
        // =====================
    // 🔥 AMBIL DATA FORM
    // =====================
    $judul = $_POST['judul'] ?? '';
    $peserta = $_POST['peserta'] ?? 0;
    $pemda = $_POST['pemda'] ?? '';
    $penyelenggara = $_POST['penyelenggara'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $tempat = $_POST['tempat'] ?? '';

    // 🔥 UPDATE DEFAULT
    $tor = uploadOrKeep('tor', $dataLama['tor'] ?? '');
    $surat_bpsdm = uploadOrKeep('surat_bpsdm', $dataLama['surat_bpsdm'] ?? '');
    $jadwal = uploadOrKeep('jadwal', $dataLama['jadwal_kegiatan'] ?? '');
    $penawaran = uploadOrKeep('penawaran', $dataLama['surat_penawaran'] ?? '');
    $mou = uploadOrKeep('mou', $dataLama['mou'] ?? '');
    $balasan = uploadOrKeep('balasan', $dataLama['surat_balasan'] ?? '');
    $akreditasi = uploadOrKeep('akreditasi', $dataLama['akreditasi'] ?? '');
    $undangan = uploadOrKeep('undangan', $dataLama['undangan_pembukaan'] ?? '');

// 🔥 UPDATE PERMOHONAN
mysqli_query($conn, "
    UPDATE permohonan SET
    judul_tema='$judul',
    jumlah_peserta='$peserta',
    pemda='$pemda',
    penyelenggara='$penyelenggara',
    tanggal_mulai='$tanggal_mulai',
    tanggal_selesai='$tanggal_selesai',
    tempat_pelaksanaan='$tempat'
    WHERE id_permohonan='$id'
");

// 🔥 UPDATE DOKUMEN
mysqli_query($conn, "
    UPDATE dokumen_permohonan SET
    tor='$tor',
    surat_bpsdm='$surat_bpsdm',
    jadwal_kegiatan='$jadwal',
    surat_penawaran='$penawaran',
    mou='$mou',
    surat_balasan='$balasan',
    akreditasi='$akreditasi',
    undangan_pembukaan='$undangan'
    WHERE id_permohonan='$id'
");

    // =====================
    // 🔥 SIMPAN SYARAT DINAMIS
    // =====================
    $syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan");

    while($s = mysqli_fetch_assoc($syarat)) {

        $field = "syarat_" . $s['id_syarat'];

        if ($s['tipe'] == 'file') {

            $cek = mysqli_query($conn, "
                SELECT * FROM isi_syarat 
                WHERE id_permohonan='$id'
                AND id_syarat='".$s['id_syarat']."'
            ");
            $old = mysqli_fetch_assoc($cek);
            $lama = $old['value'] ?? '';

            if (!empty($_FILES[$field]['name'])) {
                $nama = time() . '_' . $_FILES[$field]['name'];
                move_uploaded_file($_FILES[$field]['tmp_name'], "../uploads/" . $nama);
            } else {
                $nama = $lama;
            }

        } else {
            $nama = $_POST[$field] ?? '';
        }

        $cek = mysqli_query($conn, "
            SELECT * FROM isi_syarat 
            WHERE id_permohonan='$id'
            AND id_syarat='".$s['id_syarat']."'
        ");

        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "
                UPDATE isi_syarat SET value='$nama'
                WHERE id_permohonan='$id'
                AND id_syarat='".$s['id_syarat']."'
            ");
        } else {
            mysqli_query($conn, "
                INSERT INTO isi_syarat (id_permohonan, id_syarat, value)
                VALUES ('$id', '".$s['id_syarat']."', '$nama')
            ");
        }
    }

    mysqli_query($conn, "
        UPDATE permohonan SET
        status_permohonan='pending',
        catatan=NULL
        WHERE id_permohonan='$id'
    ");

    header("Location: laporan/laporan.php");
    exit;
}

// 🔥 ambil syarat lama
$uploaded = [];
$qFile = mysqli_query($conn, "
    SELECT * FROM isi_syarat WHERE id_permohonan='$id'
");
while($d = mysqli_fetch_assoc($qFile)){
    $uploaded[$d['id_syarat']] = $d['value'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Permohonan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
<div class="container">

<h4 class="mb-4">✏️ Edit Dokumen Permohonan</h4>

<?php if(!empty($data['catatan'])) { ?>
<div class="alert alert-danger">
<b>Catatan Admin:</b><br>
<?= $data['catatan'] ?>
</div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

<h6 class="mb-3">📎 Upload Dokumen</h6>

<div class="row g-3">

<!-- 🔥 DEFAULT -->
<?php
function viewFile($file){
    if(!empty($file)){
        return '
        <div class="p-2 border rounded bg-light mt-1">
            <small>Kosongkan jika tidak ingin mengganti</small>
            <div>
                📄 <a href="../uploads/'.$file.'" target="_blank">'.$file.'</a>
            </div>
        </div>';
    }
}
?>

<div class="col-md-6">
<label>Judul</label>
<input type="text" name="judul" class="form-control"
value="<?= $data['judul_tema'] ?>">
</div>

<div class="col-md-6">
<label>Jumlah Peserta</label>
<input type="number" name="peserta" class="form-control"
value="<?= $data['jumlah_peserta'] ?>">
</div>

<div class="col-md-6">
<label>Pemda</label>
<input type="text" name="pemda" class="form-control"
value="<?= $data['pemda'] ?>">
</div>

<div class="col-md-6">
<label>Penyelenggara</label>
<input type="text" name="penyelenggara" class="form-control"
value="<?= $data['penyelenggara'] ?>">
</div>

<div class="col-md-6">
<label>Tanggal Mulai</label>
<input type="date" name="tanggal_mulai" class="form-control"
value="<?= $data['tanggal_mulai'] ?>">
</div>

<div class="col-md-6">
<label>Tanggal Selesai</label>
<input type="date" name="tanggal_selesai" class="form-control"
value="<?= $data['tanggal_selesai'] ?>">
</div>

<div class="col-md-6">
<label>Tempat</label>
<input type="text" name="tempat" class="form-control"
value="<?= $data['tempat_pelaksanaan'] ?>">
</div>

<?php
$syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan WHERE tipe='text'");
while($s = mysqli_fetch_assoc($syarat)) {

    $value = $uploaded[$s['id_syarat']] ?? '';
?>

<div class="col-md-6">
    <label><?= $s['nama_syarat'] ?></label>
    <input type="text"
           name="syarat_<?= $s['id_syarat'] ?>"
           class="form-control"
           value="<?= $value ?>">
</div>

<?php } ?>

<!-- 🔥 PEMBATAS -->
<hr class="my-4">

<h5 class="mb-3">📎 Upload Dokumen</h5>

<div class="col-md-6">
<label>TOR</label>
<input type="file" name="tor" class="form-control">
<?= viewFile($data['tor'] ?? '') ?>
</div>

<div class="col-md-6">
<label>Surat BPSDM</label>
<input type="file" name="surat_bpsdm" class="form-control">
<?= viewFile($data['surat_bpsdm'] ?? '') ?>
</div>

<div class="col-md-6">
<label>Jadwal</label>
<input type="file" name="jadwal" class="form-control">
<?= viewFile($data['jadwal_kegiatan'] ?? '') ?>
</div>

<div class="col-md-6">
<label>Surat Penawaran</label>
<input type="file" name="penawaran" class="form-control">
<?= viewFile($data['surat_penawaran'] ?? '') ?>
</div>

<div class="col-md-6">
<label>MOU</label>
<input type="file" name="mou" class="form-control">
<?= viewFile($data['mou'] ?? '') ?>
</div>

<div class="col-md-6">
<label>Surat Balasan</label>
<input type="file" name="balasan" class="form-control">
<?= viewFile($data['surat_balasan'] ?? '') ?>
</div>

<div class="col-md-6">
<label>Akreditasi</label>
<input type="file" name="akreditasi" class="form-control">
<?= viewFile($data['akreditasi'] ?? '') ?>
</div>

<div class="col-md-6">
<label>Undangan</label>
<input type="file" name="undangan" class="form-control">
<?= viewFile($data['undangan_pembukaan'] ?? '') ?>
</div>

<!-- 🔥 SYARAT TAMBAHAN (LANJUT DI BAWAH TANPA SECTION BARU) -->
<?php
$syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan WHERE tipe='file'");
while($s = mysqli_fetch_assoc($syarat)) {

$file_lama = $uploaded[$s['id_syarat']] ?? '';
?>

<div class="col-md-6">
<label><?= $s['nama_syarat'] ?></label>

<input type="file" name="syarat_<?= $s['id_syarat'] ?>" class="form-control">

<?= viewFile($file_lama) ?>

</div>

<?php } ?>

</div>

<div class="mt-4 d-flex gap-2">
<button type="submit" name="update" class="btn btn-primary">
💾 Simpan Perubahan
</button>

<a href="laporan/laporan.php" class="btn btn-secondary">
← Kembali
</a>
</div>

</form>

</div>
</body>
</html>
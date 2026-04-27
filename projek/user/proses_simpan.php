<?php
session_start();
include '../config/koneksi.php';


// 🔥 CEK SESSION
if (!isset($_SESSION['form']) || !isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}


$data = $_SESSION['form'];
$uploaded = $_SESSION['uploaded'];
$id_user = $_SESSION['id_user'];
$id_admin = 1; 

// =====================
// AMBIL DATA
// =====================
$judul = $data['judul'] ?? '';
$peserta = $data['peserta'] ?? 0;
$tempat = $data['tempat'] ?? '';
$id_jenis_kegiatan = $data['id_jenis_kegiatan'] ?? 0;
$pemda = $data['pemda'] ?? '';
$penyelenggara = $data['penyelenggara'] ?? '';
$tanggal_mulai = $data['tanggal_mulai'] ?? '';
$tanggal_selesai = $data['tanggal_selesai'] ?? '';

$status = 'pending';
$tanggal_permohonan = date('Y-m-d H:i:s');

// =====================
// INSERT PERMOHONAN
// =====================
$insert = mysqli_query($conn, "
INSERT INTO permohonan
(judul_tema, jumlah_peserta, pemda, penyelenggara, tanggal_mulai, tanggal_selesai, tempat_pelaksanaan, tanggal_permohonan, status_permohonan, id_user, id_admin, id_jenis_kegiatan)
VALUES
('$judul','$peserta','$pemda','$penyelenggara','$tanggal_mulai','$tanggal_selesai','$tempat','$tanggal_permohonan','$status','$id_user','$id_admin','$id_jenis_kegiatan')
");

if (!$insert) {
    die("Gagal simpan permohonan: " . mysqli_error($conn));
}

$id_permohonan = mysqli_insert_id($conn);

// =====================
// AMBIL FILE DARI SESSION
// =====================
$uploaded = $_SESSION['uploaded'] ?? [];

$tor = $uploaded['tor'] ?? '';
$surat_bpsdm = $uploaded['surat_bpsdm'] ?? '';
$jadwal = $uploaded['jadwal'] ?? '';
$penawaran = $uploaded['penawaran'] ?? '';
$mou = $uploaded['mou'] ?? '';
$balasan = $uploaded['balasan'] ?? '';
$akreditasi = $uploaded['akreditasi'] ?? '';
$undangan = $uploaded['undangan'] ?? '';

// =====================
// INSERT DOKUMEN
// =====================
$insertDoc = mysqli_query($conn, "
INSERT INTO dokumen_permohonan  
(id_permohonan, tor, surat_bpsdm, jadwal_kegiatan, surat_penawaran, mou, surat_balasan, akreditasi, undangan_pembukaan)
VALUES
('$id_permohonan', '$tor', '$surat_bpsdm', '$jadwal', '$penawaran', '$mou', '$balasan', '$akreditasi', '$undangan')
");

if (!$insertDoc) {
    die("Gagal simpan dokumen: " . mysqli_error($conn));
}

// =====================
// 🔥 SIMPAN SYARAT DINAMIS
// =====================
$syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan");

while($s = mysqli_fetch_assoc($syarat)) {

    $id_syarat = $s['id_syarat'];
    $field = 'syarat_'.$id_syarat;

    if ($s['tipe'] == 'file') {
        $value = $uploaded[$field] ?? '';
    } else {
        $value = $data[$field] ?? '';
    }

    mysqli_query($conn, "
        INSERT INTO isi_syarat (id_permohonan, id_syarat, value)
        VALUES ('$id_permohonan', '$id_syarat', '$value')
    ");
}

// =====================
// BERSIHKAN SESSION
// =====================
unset($_SESSION['uploaded']);
unset($_SESSION['form']);
unset($_SESSION['files']);

// =====================
// REDIRECT
// =====================
echo "<script>
alert('Berhasil disimpan!');
window.location='index.php';
</script>";
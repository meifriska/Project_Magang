<?php

session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$nama = $_SESSION['nama'];
$id = $_GET['id'];

// 🔥 HANDLE AKSI
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $aksi = $_POST['aksi'];

    if ($aksi == 'setujui') {
        $status = 'disetujui';
    } else if ($aksi == 'tolak') {
        $status = 'ditolak';
        // 🔥 TAMBAHKAN INI (RESET LAPORAN)
            mysqli_query($conn, "
                UPDATE dokumen_permohonan 
                SET laporan_kegiatan = NULL 
                WHERE id_permohonan = '$id'
            ");
    }

    $catatanGabung = "";

    foreach ($_POST as $key => $value) {

        if ($key == 'catatan_final') continue;

        if (strpos($key, 'catatan_') === 0 && !empty($value)) {

            $namaField = str_replace('catatan_', '', $key);
            $label = strtoupper(str_replace('_', ' ', $namaField));

            $catatanGabung .= "• $label : $value\n";
        }
    }

    if (!empty($_POST['catatan_final'])) {
        $catatanGabung .= "\nKESIMPULAN:\n" . $_POST['catatan_final'];
    }

    mysqli_query($conn, "
        UPDATE permohonan
        SET status_permohonan = '$status',
            catatan = '$catatanGabung'
        WHERE id_permohonan = '$id'
    ");

    header("Location: permohonan.php?msg=berhasil");
    exit;
}

// 🔥 AMBIL DATA
$q = mysqli_query($conn, "
    SELECT p.*,
           d.tor, d.surat_bpsdm, d.jadwal_kegiatan,
           d.surat_penawaran, d.mou, d.surat_balasan,
           d.akreditasi, d.undangan_pembukaan
    FROM permohonan p
    LEFT JOIN dokumen_permohonan d
    ON p.id_permohonan = d.id_permohonan
    WHERE p.id_permohonan = '$id'
");

$syarat = mysqli_query($conn, "
    SELECT s.*, i.value
    FROM syarat_permohonan s
    LEFT JOIN isi_syarat i 
    ON s.id_syarat = i.id_syarat 
    AND i.id_permohonan = '$id'
");

$data = mysqli_fetch_assoc($q);

// 🔥 PECAH CATATAN LAMA (EDIT MODE)
$catatanLama = [];
$kesimpulan = '';

$catatanLama = [];
$kesimpulan = '';
$ambilKesimpulan = false;

if (!empty($data['catatan'])) {

    $lines = explode("\n", $data['catatan']);

    foreach ($lines as $line) {

        // 🔥 AMBIL CATATAN PER ITEM
        if (preg_match('/• (.*?) : (.*)/', $line, $match)) {
            $key = strtolower(str_replace(' ', '_', $match[1]));
            $catatanLama[$key] = $match[2];
        }

        // 🔥 DETEKSI MULAI KESIMPULAN
        if (trim($line) == 'KESIMPULAN:') {
            $ambilKesimpulan = true;
            continue;
        }

        // 🔥 AMBIL ISI KESIMPULAN (BARIS SETELAHNYA)
        if ($ambilKesimpulan) {
            $kesimpulan .= $line . "\n";
        }
    }

    $kesimpulan = trim($kesimpulan);
}

// 🔥 KOMPONEN
function komponen($label, $value, $name) {
    global $catatanLama;
?>
<div class="row align-items-center border-bottom py-3">
    <div class="col-md-3 fw-semibold"><?= $label ?></div>
    <div class="col-md-4 text-muted">
        <?= $value ?: '-' ?>
    </div>
    <div class="col-md-5">
        <input type="text"
               name="catatan_<?= $name ?>"
               class="form-control form-control-sm"
               placeholder="Catatan..."
               value="<?= $catatanLama[$name] ?? '' ?>">
    </div>
</div>
<?php
}

function dokumenItem($label, $file, $name) {
    global $catatanLama;

    $path = "../uploads/" . $file;
    $exists = !empty($file); // 🔥 cukup cek dari DB
?>
<div class="row align-items-center border-bottom py-3">

    <div class="col-md-3 fw-semibold">
        <i class="bi bi-file-earmark-text"></i> <?= $label ?>
    </div>

    <?php
    echo "<pre>";
    var_dump($file);
    echo "</pre>";
    ?>
    <div class="col-md-3">
        <a href="<?= $exists ? $path : 'javascript:void(0)' ?>"
           target="_blank"
           class="btn btn-sm btn-outline-primary"
           onclick="<?= !$exists ? "alert('Dokumen Tidak Tersedia!'); return false;" : '' ?>">
            <i class="bi bi-eye"></i> Lihat
        </a>
    </div>

    <div class="col-md-6">
        <input type="text"
               name="catatan_<?= $name ?>"
               class="form-control form-control-sm"
               placeholder="Catatan dokumen..."
               value="<?= $catatanLama[$name] ?? '' ?>">
    </div>

</div>
<?php
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Detail Permohonan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { font-size: 14px; }

.card-body .row:last-child {
    border-bottom: none !important;
}

/* 🔥 NOTIF TENGAH */
#notifArea {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(0,0,0,0.5);
    z-index: 9999;

    visibility: hidden;
    opacity: 0;
    transition: 0.3s;
}

#notifArea.show {
    visibility: visible;
    opacity: 1;
}
</style>
</head>

<body class="p-4 bg-light">

<div id="notifArea"></div>

<div class="container">

<h4 class="mb-3">
<i class="bi bi-check2-square"></i> Verifikasi Permohonan
</h4>

<form method="POST" id="formUtama">

<!-- INFORMASI -->
<div class="card mb-3 shadow-sm">
<div class="card-body">

<h6 class="mb-3">
<i class="bi bi-info-circle"></i> Informasi Kegiatan
</h6>

<?php
komponen("Judul / Tema", $data['judul_tema'], "judul");
komponen("Pemda", $data['pemda'], "pemda");
komponen("Penyelenggara", $data['penyelenggara'], "penyelenggara");
komponen("Jumlah Peserta", $data['jumlah_peserta'], "peserta");
komponen("Tanggal", $data['tanggal_mulai']." - ".$data['tanggal_selesai'], "tanggal");
komponen("Tempat", $data['tempat_pelaksanaan'], "tempat");
$syaratText = mysqli_query($conn, "
    SELECT s.*, i.value
    FROM syarat_permohonan s
    LEFT JOIN isi_syarat i 
    ON s.id_syarat = i.id_syarat 
    AND i.id_permohonan = '$id'
    WHERE s.tipe = 'text'
");

while($s = mysqli_fetch_assoc($syaratText)) {
    komponen($s['nama_syarat'], $s['value'], 'syarat_'.$s['id_syarat']);
}
?>

</div>
</div>


<!-- DOKUMEN -->
<div class="card mb-3 shadow-sm">
<div class="card-body">

<h6 class="mb-3">
<i class="bi bi-folder2-open"></i> Verifikasi Dokumen
</h6>

<?php
dokumenItem("TOR", $data['tor'], "tor");
dokumenItem("Surat BPSDM", $data['surat_bpsdm'], "surat_bpsdm");
dokumenItem("Jadwal", $data['jadwal_kegiatan'], "jadwal");
dokumenItem("Surat Penawaran", $data['surat_penawaran'], "penawaran");
dokumenItem("MoU", $data['mou'], "mou");
dokumenItem("Surat Balasan", $data['surat_balasan'], "balasan");
dokumenItem("Akreditasi", $data['akreditasi'], "akreditasi");
dokumenItem("Undangan", $data['undangan_pembukaan'], "undangan");
$syaratFile = mysqli_query($conn, "
    SELECT s.*, i.value
    FROM syarat_permohonan s
    LEFT JOIN isi_syarat i 
    ON s.id_syarat = i.id_syarat 
    AND i.id_permohonan = '$id'
    WHERE s.tipe = 'file'
");

while($s = mysqli_fetch_assoc($syaratFile)) {
    dokumenItem($s['nama_syarat'], $s['value'], 'syarat_'.$s['id_syarat']);
}
?>

</div>
</div>

<!-- FINAL -->
<div class="card shadow-sm">
<div class="card-body">

<h5>
<i class="bi bi-check-circle"></i> Keputusan Akhir
</h5>

<textarea name="catatan_final"
class="form-control mb-3"
placeholder="Catatan akhir..."><?= $kesimpulan ?></textarea>

</div>
</div>

</form>

<!-- BUTTON -->
<div class="d-flex justify-content-between mt-3">

    <a href="permohonan.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

    <div>
        <button form="formUtama" name="aksi" value="setujui" class="btn btn-success">
            <i class="bi bi-check-lg"></i> Setujui
        </button>

        <button form="formUtama" name="aksi" value="tolak" class="btn btn-danger">
            <i class="bi bi-x-lg"></i> Tolak
        </button>
    </div>

</div>

</div>

<script>
function cekFile(exists, path) {
    if (!exists) {
        showNotif("⚠️ Dokumen tidak tersedia!");
    } else {
        window.open(path, "_blank");
    }
}

function showNotif(message) {
    const notifArea = document.getElementById("notifArea");

    notifArea.innerHTML = `
        <div class="bg-white p-4 rounded shadow text-center" style="min-width:320px;">
            <div class="mb-2 text-danger" style="font-size:40px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="mb-3 fw-semibold">
                ${message}
            </div>
            <button class="btn btn-danger btn-sm px-4" onclick="closeNotif()">
                Tutup
            </button>
        </div>
    `;

    notifArea.classList.add("show");
}

function closeNotif() {
    document.getElementById("notifArea").classList.remove("show");
}
</script>

</body>
</html>
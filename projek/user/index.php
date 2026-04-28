<?php
session_start();
include '../config/koneksi.php';

$id_user = $_SESSION['id_user'];

$cekUser = mysqli_query($conn, "SELECT status_akun FROM user WHERE id_user='$id_user'");
$dataUser = mysqli_fetch_assoc($cekUser);

// 🔥 UPDATE SESSION BIAR SELALU SYNC
$_SESSION['status'] = $dataUser['status_akun'];


$id_user = $_SESSION['id_user'];
$nama = $_SESSION['nama'] ?? 'User';

// 🔥 CEK BLOKIR
$cek = mysqli_query($conn, "
    SELECT p.*, d.laporan_kegiatan
    FROM permohonan p
    LEFT JOIN dokumen_permohonan d 
    ON p.id_permohonan = d.id_permohonan
    WHERE p.id_user='$id_user'
    AND p.status_permohonan='disetujui'
");

$blokir = false;

while($c = mysqli_fetch_assoc($cek)) {

    if (empty($c['laporan_kegiatan'])) {

        $tanggal_selesai = strtotime($c['tanggal_selesai']);
        $batas = strtotime("+12 days", $tanggal_selesai);
        $sekarang = time();

        if ($sekarang > $batas) {
            $blokir = true;
            break;
        }
    }
}

if (isset($_POST['simpan'])) {

    $_SESSION['form'] = $_POST;

    $folder = "../uploads/";

    function uploadPreview($name, $folder) {
        if ($_FILES[$name]['name'] == '') return null;

        $nama = time() . '_' . $_FILES[$name]['name'];
        move_uploaded_file($_FILES[$name]['tmp_name'], $folder . $nama);

        return $nama;
    }

    $_SESSION['uploaded'] = [
        'tor' => uploadPreview('tor', $folder),
        'surat_bpsdm' => uploadPreview('surat_bpsdm', $folder),
        'jadwal' => uploadPreview('jadwal', $folder),
        'penawaran' => uploadPreview('penawaran', $folder),
        'mou' => uploadPreview('mou', $folder),
        'balasan' => uploadPreview('balasan', $folder),
        'akreditasi' => uploadPreview('akreditasi', $folder),
        'undangan' => uploadPreview('undangan', $folder),

    ];

    // 🔥 TAMBAHAN WAJIB (SYARAT FILE DINAMIS)
        $syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan WHERE tipe='file'");

        while($s = mysqli_fetch_assoc($syarat)) {

            $field = 'syarat_'.$s['id_syarat'];

            if (!empty($_FILES[$field]['name'])) {

                $nama_file = time() . '_' . $_FILES[$field]['name'];

                move_uploaded_file(
                    $_FILES[$field]['tmp_name'],
                    $folder . $nama_file
                );

                $_SESSION['uploaded'][$field] = $nama_file;
            }
        }
    header("Location: preview.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <a href="../index.php">🏠 Beranda</a>
        </li>

        <li class="<?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">
            <a href="laporan/laporan.php">📤 Laporan Kegiatan</a>
        </li>

        <li class="<?= basename($_SERVER['PHP_SELF']) == 'riwayat.php' ? 'active' : '' ?>">
            <a href="riwayat.php">🕘 Riwayat</a>
        </li>
        <li>
            <a href="chat.php">💬 Chat Admin</a>
        </li>
    </ul>

    <div class="help-box">
        <small>BUTUH BANTUAN?</small>
        <p>Hubungi admin jika mengalami kendala.</p>
        <button class="btn btn-light btn-sm w-100">Pusat Bantuan</button>
    </div>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="topbar">
        <div>
            <h5>Selamat datang, <strong><?= $nama ?></strong></h5>
            <small><?= date('l, d F Y') ?> • Layanan DPRD Jatim</small>
        </div>

        <div class="user-box position-relative" id="userBox">
            <span><?= $nama ?></span>
            <div class="avatar-icon" id="avatarBtn">
                <i class="bi bi-person-fill"></i>
            </div>
            <!-- DROPDOWN -->
            <div class="dropdown-user" id="dropdownUser">
                <a href="#" onclick="confirmLogout()">Logout</a>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

    <!-- HERO -->
    <div class="hero">
            <small>🟢 SISTEM ONLINE TERINTEGRASI</small>
            <h3>Pengajuan Rekomendasi Kegiatan DPRD Jatim</h3>
            <p>Silahkan lengkapi informasi detail kegiatan yang akan dilaksanakan untuk mendapatkan surat rekomendasi resmi dari sekretariat DPRD Provinsi Jawa Timur.</p>
        </div>

    <?php
    // 🔒 PRIORITAS 1: akun diblokir
    if (isset($_SESSION['status']) && $_SESSION['status'] == 'tidak aktif') {
    ?>

    <div class="alert alert-danger">
        🔒 Akun Anda dinonaktifkan. <br>
        Silakan hubungi admin untuk mengaktifkan kembali akun Anda.
    </div>

    <?php return; } ?>


    <?php if ($blokir) { ?>

    <div class="alert alert-danger">
        ❌ Kamu telat upload laporan (lebih dari 12 hari).  
        ayo upload segera agar bisa mengakses fitur ini.
    </div>

    <?php return; } ?>

    <?php if ($_SESSION['status'] != 'nonaktif'): ?>
    <!-- FORM -->
    <form method="POST" enctype="multipart/form-data">

        <div class="card form-card">

            <h6 class="mb-3">📄 Input Data Permohonan</h6>

            <div class="row g-3">

                <div class="col-md-6">
                    <label>Jenis Layanan</label>
                    <select name="jenis_layanan" class="form-control">
                        <option>Permohonan Rekomendasi</option>
                    </select>
                </div>

                <div class="col-md-6">
            <label>Pemerintah Daerah</label>
            <select name="pemda" id="pemda" class="form-control select2" required onchange="loadInstansi()">
                <option value="">-- Pilih Daerah --</option>

                <!-- KOTA -->
                <option value="Surabaya" <?= ($form['pemda'] ?? '') == 'Surabaya' ? 'selected' : '' ?>>Surabaya</option>
                <option value="Malang" <?= ($form['pemda'] ?? '') == 'Malang' ? 'selected' : '' ?>>Malang</option>
                <option value="Kediri" <?= ($form['pemda'] ?? '') == 'Kediri' ? 'selected' : '' ?>>Kediri</option>
                <option value="Blitar" <?= ($form['pemda'] ?? '') == 'Blitar' ? 'selected' : '' ?>>Blitar</option>
                <option value="Madiun" <?= ($form['pemda'] ?? '') == 'Madiun' ? 'selected' : '' ?>>Madiun</option>
                <option value="Pasuruan" <?= ($form['pemda'] ?? '') == 'Pasuruan' ? 'selected' : '' ?>>Pasuruan</option>
                <option value="Probolinggo" <?= ($form['pemda'] ?? '') == 'Probolinggo' ? 'selected' : '' ?>>Probolinggo</option>
                <option value="Mojokerto" <?= ($form['pemda'] ?? '') == 'Mojokerto' ? 'selected' : '' ?>>Mojokerto</option>
                <option value="Batu" <?= ($form['pemda'] ?? '') == 'Batu' ? 'selected' : '' ?>>Batu</option>

                <!-- KABUPATEN -->
                <option value="Bangkalan" <?= ($form['pemda'] ?? '') == 'Bangkalan' ? 'selected' : '' ?>>Bangkalan</option>
                <option value="Banyuwangi" <?= ($form['pemda'] ?? '') == 'Banyuwangi' ? 'selected' : '' ?>>Banyuwangi</option>
                <option value="Bojonegoro" <?= ($form['pemda'] ?? '') == 'Bojonegoro' ? 'selected' : '' ?>>Bojonegoro</option>
                <option value="Bondowoso" <?= ($form['pemda'] ?? '') == 'Bondowoso' ? 'selected' : '' ?>>Bondowoso</option>
                <option value="Gresik" <?= ($form['pemda'] ?? '') == 'Gresik' ? 'selected' : '' ?>>Gresik</option>
                <option value="Jember" <?= ($form['pemda'] ?? '') == 'Jember' ? 'selected' : '' ?>>Jember</option>
                <option value="Jombang" <?= ($form['pemda'] ?? '') == 'Jombang' ? 'selected' : '' ?>>Jombang</option>
                <option value="Lamongan" <?= ($form['pemda'] ?? '') == 'Lamongan' ? 'selected' : '' ?>>Lamongan</option>
                <option value="Lumajang" <?= ($form['pemda'] ?? '') == 'Lumajang' ? 'selected' : '' ?>>Lumajang</option>
                <option value="Magetan" <?= ($form['pemda'] ?? '') == 'Magetan' ? 'selected' : '' ?>>Magetan</option>
                <option value="Nganjuk" <?= ($form['pemda'] ?? '') == 'Nganjuk' ? 'selected' : '' ?>>Nganjuk</option>
                <option value="Ngawi" <?= ($form['pemda'] ?? '') == 'Ngawi' ? 'selected' : '' ?>>Ngawi</option>
                <option value="Pacitan" <?= ($form['pemda'] ?? '') == 'Pacitan' ? 'selected' : '' ?>>Pacitan</option>
                <option value="Pamekasan" <?= ($form['pemda'] ?? '') == 'Pamekasan' ? 'selected' : '' ?>>Pamekasan</option>
                <option value="Ponorogo" <?= ($form['pemda'] ?? '') == 'Ponorogo' ? 'selected' : '' ?>>Ponorogo</option>
                <option value="Sampang" <?= ($form['pemda'] ?? '') == 'Sampang' ? 'selected' : '' ?>>Sampang</option>
                <option value="Sidoarjo" <?= ($form['pemda'] ?? '') == 'Sidoarjo' ? 'selected' : '' ?>>Sidoarjo</option>
                <option value="Situbondo" <?= ($form['pemda'] ?? '') == 'Situbondo' ? 'selected' : '' ?>>Situbondo</option>
                <option value="Sumenep" <?= ($form['pemda'] ?? '') == 'Sumenep' ? 'selected' : '' ?>>Sumenep</option>
                <option value="Trenggalek" <?= ($form['pemda'] ?? '') == 'Trenggalek' ? 'selected' : '' ?>>Trenggalek</option>
                <option value="Tuban" <?= ($form['pemda'] ?? '') == 'Tuban' ? 'selected' : '' ?>>Tuban</option>
                <option value="Tulungagung" <?= ($form['pemda'] ?? '') == 'Tulungagung' ? 'selected' : '' ?>>Tulungagung</option>

            </select>
        </div>

        <div class="col-md-6">
            <label>Jenis Kegiatan</label>
            <select name="id_jenis_kegiatan" class="form-control">

            <?php
            $q = mysqli_query($conn, "SELECT * FROM jenis_kegiatan");
            while ($d = mysqli_fetch_assoc($q)) {

                $selected = ($form['id_jenis_kegiatan'] ?? '') == $d['id_jenis_kegiatan'] ? 'selected' : '';

                echo "<option value='".$d['id_jenis_kegiatan']."' $selected>".$d['nama_kegiatan']."</option>";
            }
            ?>

            </select>
        </div>

        <div class="col-md-6">
            <label>Judul Tema</label>
            <input type="text" name="judul" class="form-control" placeholder="Masukkan judul"
            value="<?= $form['judul'] ?? '' ?>" required>
        </div>

        <div class="col-md-6">
            <label>Tanggal Pelaksanaan</label>
            <div class="d-flex align-items-center gap-2">
                <input type="date" name="tanggal_mulai" class="form-control w-50"
                value="<?= $form['tanggal_mulai'] ?? '' ?>" required>
                <span>-</span>
                <input type="date" name="tanggal_selesai" class="form-control w-50"
                value="<?= $form['tanggal_selesai'] ?? '' ?>" required>
            </div>
        </div>

        <div class="col-md-6">
            <label>Tempat Pelaksanaan</label>
            <input type="text" name="tempat" class="form-control"
            value="<?= $form['tempat'] ?? '' ?>" required>
        </div>

        <div class="col-md-6">
            <label>Jumlah Peserta</label>
            <input type="number" name="peserta" class="form-control" required
            value="<?= $form['peserta'] ?? '' ?>">
        </div>

        <div class="col-md-6">
            <label>Penyelenggara</label>
            <select name="penyelenggara" id="penyelenggara" class="form-control" required>
                <option value="">-- Pilih Penyelenggara --</option>
                <?php if (!empty($form['penyelenggara'])): ?>
                    <option value="<?= $form['penyelenggara'] ?>" selected>
                        <?= $form['penyelenggara'] ?>
                    </option>
                <?php endif; ?>
            </select>
        </div>

        <?php
        $syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan WHERE tipe='text'");

        while($s = mysqli_fetch_assoc($syarat)) {
        ?>
        <div class="col-md-6">
            <label><?= $s['nama_syarat'] ?></label>
            <input type="text" 
                name="syarat_<?= $s['id_syarat'] ?>" 
                class="form-control">
        </div>
        <?php } ?>

    </div>

    <!-- 🔥 UPLOAD DI SINI -->
   <hr class="my-4">

    <h6 class="mb-3">📎 Upload Dokumen</h6>

    <div class="row row-cols-1 row-cols-md-2 g-3">

    <!-- TOR -->
    <div class="col">
        <label>TOR (Term of Reference)</label>
        <input type="file" name="tor" class="form-control mb-2">

        <?php if (!empty($uploaded['tor'])): ?>
        <div class="p-2 border rounded bg-light">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            <div>
                📄 <a href="../uploads/<?= $uploaded['tor'] ?>" target="_blank">
                    <?= $uploaded['tor'] ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- BPSDM -->
    <div class="col">
        <label>Surat Permohonan BPSDM</label>
        <input type="file" name="surat_bpsdm" class="form-control mb-2">

        <?php if (!empty($uploaded['surat_bpsdm'])): ?>
        <div class="p-2 border rounded bg-light">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            <div>
                📄 <a href="../uploads/<?= $uploaded['surat_bpsdm'] ?>" target="_blank">
                    <?= $uploaded['surat_bpsdm'] ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- JADWAL -->
    <div class="col">
        <label>Jadwal Kegiatan</label>
        <input type="file" name="jadwal" class="form-control mb-2">

        <?php if (!empty($uploaded['jadwal'])): ?>
        <div class="p-2 border rounded bg-light">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            <div>
                📄 <a href="../uploads/<?= $uploaded['jadwal'] ?>" target="_blank">
                    <?= $uploaded['jadwal'] ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- PENAWARAN -->
    <div class="col">
        <label>Surat Penawaran</label>
        <input type="file" name="penawaran" class="form-control mb-2">

        <?php if (!empty($uploaded['penawaran'])): ?>
        <div class="p-2 border rounded bg-light">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            <div>
                📄 <a href="../uploads/<?= $uploaded['penawaran'] ?>" target="_blank">
                    <?= $uploaded['penawaran'] ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- MOU -->
    <div class="col">
        <label>MOU</label>
        <input type="file" name="mou" class="form-control mb-2">

        <?php if (!empty($uploaded['mou'])): ?>
        <div class="p-2 border rounded bg-light">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            <div>
                📄 <a href="../uploads/<?= $uploaded['mou'] ?>" target="_blank">
                    <?= $uploaded['mou'] ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- BALASAN -->
    <div class="col">
        <label>Surat Balasan Kab/Kota</label>
        <input type="file" name="balasan" class="form-control mb-2">

        <?php if (!empty($uploaded['balasan'])): ?>
        <div class="p-2 border rounded bg-light">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            <div>
                📄 <a href="../uploads/<?= $uploaded['balasan'] ?>" target="_blank">
                    <?= $uploaded['balasan'] ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- AKREDITASI -->
    <div class="col">
        <label>Akreditasi (Khusus Universitas)</label>
        <input type="file" name="akreditasi" class="form-control mb-2">

        <?php if (!empty($uploaded['akreditasi'])): ?>
        <div class="p-2 border rounded bg-light">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            <div>
                📄 <a href="../uploads/<?= $uploaded['akreditasi'] ?>" target="_blank">
                    <?= $uploaded['akreditasi'] ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- UNDANGAN -->
    <div class="col">
        <label>Undangan Pembukaan</label>
        <input type="file" name="undangan" class="form-control mb-2">

        <?php if (!empty($uploaded['undangan'])): ?>
        <div class="p-2 border rounded bg-light">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            <div>
                📄 <a href="../uploads/<?= $uploaded['undangan'] ?>" target="_blank">
                    <?= $uploaded['undangan'] ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- 🔥 SYARAT TAMBAHAN -->
    <?php
    $syarat = mysqli_query($conn, "SELECT * FROM syarat_permohonan WHERE tipe='file'");
    while($s = mysqli_fetch_assoc($syarat)) {
    ?>
    <div class="col">
        <label><?= $s['nama_syarat'] ?></label>
        <input type="file"
               name="syarat_<?= $s['id_syarat'] ?>"
               class="form-control">
    </div>
    <?php } ?>

    </div>

    <!-- TOMBOL FIX -->
    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" onclick="batalForm()" class="btn btn-light">
            Batal
        </button>
        <button type="submit" name="simpan" class="btn btn-primary">
            💾 Simpan Data 
        </button>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelector("form").addEventListener("submit", function(e) {

    const pemda = document.querySelector("[name='pemda']").value.trim();
    const judul = document.querySelector("[name='judul']").value.trim();
    const peserta = document.querySelector("[name='peserta']").value.trim();
    const tempat = document.querySelector("[name='tempat']").value.trim();
    const penyelenggara = document.querySelector("[name='penyelenggara']").value.trim();
    const mulai = document.querySelector("[name='tanggal_mulai']").value;
    const selesai = document.querySelector("[name='tanggal_selesai']").value;

    // 🔴 CEK KOSONG
    if (!pemda || !judul || !peserta || !tempat || !penyelenggara || !mulai || !selesai) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Form belum lengkap!',
            text: 'Harap isi semua data terlebih dahulu'
        });
        return;
    }

    // 🔴 CEK PEMDA (tidak boleh angka semua)
    if (!/^[a-zA-Z\s]+$/.test(pemda)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Pemerintah Daerah tidak valid!',
            text: 'Hanya boleh huruf'
        });
        return;
    }

    // 🔴 CEK TEMPAT
    if (!/^[a-zA-Z\s]+$/.test(tempat)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Tempat tidak valid!',
            text: 'Hanya boleh huruf'
        });
        return;
    }

    // 🔴 CEK PENYELENGGARA
    if (!/^[a-zA-Z\s]+$/.test(penyelenggara)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Penyelenggara tidak valid!',
            text: 'Hanya boleh huruf'
        });
        return;
    }

    // 🔴 CEK PESERTA HARUS ANGKA
    if (isNaN(peserta) || peserta <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Jumlah peserta tidak valid!',
            text: 'Harus berupa angka dan lebih dari 0'
        });
        return;
    }

    // 🔴 CEK TANGGAL
    if (mulai > selesai) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Tanggal tidak valid!',
            text: 'Tanggal selesai harus setelah tanggal mulai'
        });
        return;
    }

});
</script>       

</form>
<?php else: ?>

<div class="alert alert-warning text-center">
    🔒 Akun Anda dinonaktifkan, tidak bisa mengajukan permohonan.
</div>

<?php endif; ?>

</div>
</div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        function batalForm() {
            Swal.fire({
                title: 'Apakah yakin ingin membatalkan permohonan?',
                text: "Data yang diisi akan hilang!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#6a4df5",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak"
            }).then((result) => {
                if (result.isConfirmed) {

                    
                    // 🔥 reload halaman tanpa session form
                    window.location.href = "index.php?reset=1";
                }
            });
        }
        </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "-- Pilih Daerah --",
        width: '100%'
    });
});
</script>

<script>
$(document).ready(function() {

    $('#pemda').on('change', function() {

        const value = $(this).val();
        const penyelenggara = $('#penyelenggara');

        // reset isi
        penyelenggara.html('<option value="">-- Pilih Penyelenggara --</option>');

        if (value !== "") {

            penyelenggara.append(
                `<option value="Sekretariat DPRD ${value}">Sekretariat DPRD ${value}</option>`
            );

            penyelenggara.append(
                `<option value="Universitas">Universitas</option>`
            );
        }

    });

});
</script>

<script>
const avatar = document.getElementById("avatarBtn");
const dropdown = document.getElementById("dropdownUser");

avatar.addEventListener("click", function() {
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
});

// klik luar = nutup dropdown
document.addEventListener("click", function(e) {
    if (!document.getElementById("userBox").contains(e.target)) {
        dropdown.style.display = "none";
    }
});
</script>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'Apakah yakin mau logout?',
        text: 'Kamu akan keluar dari sistem',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4b2aad',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../auth/logout.php';
        }
    });
}
</script>

<script>
function loadInstansi() {
    let daerah = document.getElementById('pemda').value;

    fetch('get_instansi.php?daerah=' + daerah)
    .then(res => res.json())
    .then(data => {
        let select = document.getElementById('penyelenggara');
        select.innerHTML = '<option value="">-- Pilih Penyelenggara --</option>';

        data.forEach(item => {
            let option = document.createElement('option');
            option.value = item.nama_instansi;
            option.textContent = item.nama_instansi;
            select.appendChild(option);
        });
    });
}
</script>
</body>
</html>
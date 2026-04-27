<?php
include '../config/koneksi.php';

$daerah = $_GET['daerah'] ?? '';

$data = [];

if (!empty($daerah)) {
    $q = mysqli_query($conn, "
        SELECT nama_instansi 
        FROM instansi 
        WHERE alamat_instansi = '$daerah'
        ORDER BY nama_instansi ASC
    ");

    while ($d = mysqli_fetch_assoc($q)) {
        $data[] = $d;
    }
}

echo json_encode($data);
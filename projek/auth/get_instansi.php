<?php
include '../config/koneksi.php';

$jenis = $_GET['jenis'] ?? '';
$selected = $_GET['selected'] ?? '';

// default option
echo "<option value=''>-- Pilih Instansi --</option>";

$query = mysqli_query($conn, "SELECT * FROM instansi WHERE jenis_instansi='$jenis'");

while ($data = mysqli_fetch_assoc($query)) {

    $isSelected = ($selected == $data['id_instansi']) ? 'selected' : '';

    echo "<option value='".$data['id_instansi']."' $isSelected>"
        .$data['nama_instansi'].
        "</option>";
}
?>
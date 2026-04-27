<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "db_permohonan_rekomendasi";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    error_log("Database connection error: " . mysqli_connect_error());
    die("Terjadi kesalahan pada sistem.");
}

mysqli_set_charset($conn, "utf8mb4");

date_default_timezone_set('Asia/Jakarta');
?>
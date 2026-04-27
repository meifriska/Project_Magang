//Admin
<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// cek role admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// cek role user
if ($_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}
?>
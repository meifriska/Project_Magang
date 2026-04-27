<?php
session_start();

// 🔥 hapus hanya data login
unset($_SESSION['login']);
unset($_SESSION['role']);
unset($_SESSION['id_user']);
unset($_SESSION['id_admin']);
unset($_SESSION['nama']);

// optional: hancurkan session
session_destroy();

// redirect
header("Location: login.php");
exit;
?>
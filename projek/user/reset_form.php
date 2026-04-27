<?php
session_start();

// 🔥 hapus semua data form & upload
unset($_SESSION['form']);
unset($_SESSION['uploaded']);

// optional (lebih bersih)
session_destroy();

// balik ke dashboard kosong
header("Location: index.php");
exit;
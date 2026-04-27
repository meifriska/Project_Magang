<?php
session_start();

// ✅ hanya hapus data form saja
unset($_SESSION['form']);
unset($_SESSION['uploaded']);

// redirect ke index (tetap login)
header("Location: index.php");
exit;
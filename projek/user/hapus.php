<?php
session_start();

$field = $_GET['field'] ?? '';

if (isset($_SESSION['uploaded'][$field])) {
    unset($_SESSION['uploaded'][$field]);
}

$_SESSION['deleted'] = $field;

header("Location: preview.php");
exit;
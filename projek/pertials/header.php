<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? $title : 'E-Rekomendasi'; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/project/assets/css/style.css">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f5f6fa;
}

/* NAVBAR */
.navbar {
    background: #ffffff;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* LEFT */
.navbar-left {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.navbar-left .title {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    letter-spacing: 0.5px;
}

.navbar-left .welcome {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
}

.navbar-left .date {
    font-size: 13px;
    color: #9ca3af;
}

/* RIGHT */
.navbar-right {
    display: flex;
    align-items: center;
    gap: 18px;
}

/* ICON STYLE */
.icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
}

/* PROFILE */
.profile {
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile img {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-info {
    line-height: 1.2;
}

.profile-info .name {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.profile-info .role {
    font-size: 12px;
    color: #9ca3af;
}

/* LOGOUT */
.logout {
    font-size: 13px;
    color: #ef4444;
    text-decoration: none;
    margin-left: 10px;
}
</style>
</head>

<body>

<div class="navbar">

    <!-- LEFT -->
    <div class="navbar-left">
        <div class="title">DPRD JATIM</div>

        <div class="welcome">
            Selamat datang, <?= $_SESSION['nama'] ?? 'User'; ?>
        </div>

        <div class="date">
            <?= date('l, d M Y'); ?> • Layanan Rekomendasi DPRD Jawa Timur
        </div>
    </div>

    <!-- RIGHT -->
    <div class="navbar-right">

        <div class="icon">🔔</div>
        <div class="icon">⚙️</div>

        <div class="profile">
            <img src="/project/assets/img/user.png">

            <div class="profile-info">
                <div class="name"><?= $_SESSION['nama'] ?? 'User'; ?></div>
                <div class="role">Admin Instansi</div>
            </div>
        </div>

        <a href="/project/auth/logout.php" class="logout">Logout</a>

    </div>

</div>
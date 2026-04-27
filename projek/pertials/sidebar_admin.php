<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>E-Rekomendasi</title>

<!-- FONT -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: #f5f6fa;
}

/* ================= SIDEBAR ================= */
.sidebar {
    width: 260px;
    height: 100vh;
    background: #f9fafb;
    position: fixed;
    top: 0;
    left: 0;
    padding: 25px 20px;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 12px;
}

.sidebar-logo {
    width: 45px;
    height: 45px;
    background: #4b2aad;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.sidebar-title {
    font-size: 14px;
    font-weight: 700;
    color: #111827;
}

.sidebar-subtitle {
    font-size: 12px;
    color: #6b7280;
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 12px;
    text-decoration: none;
    color: #374151;
    font-size: 14px;
    transition: 0.2s;
}

.menu-item:hover {
    background: #ede9fe;
    color: #4b2aad;
}

.menu-item.active {
    background: #4b2aad;
    color: white;
    box-shadow: 0 6px 15px rgba(75, 42, 173, 0.3);
}

.menu-section {
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    margin-top: 15px;
}

/* ================= HEADER ================= */
.navbar {
    margin-left: 260px;
    background: #ffffff;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar-left {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.navbar-left .title {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
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

.navbar-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile {
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile img {
    width: 38px;
    height: 38px;
    border-radius: 50%;
}

.profile-info .name {
    font-size: 14px;
    font-weight: 600;
}

.profile-info .role {
    font-size: 12px;
    color: #9ca3af;
}

.logout {
    color: red;
    font-size: 13px;
    text-decoration: none;
}

/* ================= CONTENT ================= */
.content {
    margin-left: 260px;
    padding: 30px;
}

/* CARD CONTOH */
.card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}
</style>
</head>

<body>

<!-- ================= SIDEBAR ================= -->
<div class="sidebar">

    <div class="sidebar-header">
        <div class="sidebar-logo">🏛️</div>
        <div>
            <div class="sidebar-title">UNIT LAYANAN</div>
            <div class="sidebar-subtitle">DPRD Jawa Timur</div>
        </div>
    </div>

    <div class="sidebar-menu">
        <a href="#" class="menu-item active">📊 Dashboard</a>
        <a href="#" class="menu-item">📥 Permohonan Masuk</a>
        <a href="#" class="menu-item">🛡️ Verifikasi</a>
        <a href="#" class="menu-item">👥 Manajemen Pengguna</a>
        <a href="#" class="menu-item">📈 Laporan</a>
        <a href="#" class="menu-item">💬 Chat</a>
    </div>

    <div class="menu-section">PENGATURAN SISTEM</div>

    <div class="sidebar-menu">
        <a href="#" class="menu-item">⚙️ Pengaturan</a>
    </div>

</div>

<!-- ================= HEADER ================= -->
<div class="navbar">

    <div class="navbar-left">
        <div class="title">DPRD JATIM</div>
        <div class="welcome">
            Selamat datang, <?= $_SESSION['nama'] ?? 'User'; ?>
        </div>
        <div class="date">
            <?= date('l, d M Y'); ?> • Layanan Rekomendasi DPRD Jawa Timur
        </div>
    </div>

    <div class="navbar-right">
        <div class="icon">🔔</div>
        <div class="icon">⚙️</div>

        <div class="profile">
            <img src="https://i.pravatar.cc/100">
            <div class="profile-info">
                <div class="name"><?= $_SESSION['nama'] ?? 'User'; ?></div>
                <div class="role">Admin Instansi</div>
            </div>
        </div>

        <a href="#" class="logout">Logout</a>
    </div>

</div>

<!-- ================= CONTENT ================= -->
<div class="content">

    <div class="card">
        <h2>Dashboard</h2>
        <p>Selamat datang di sistem E-Rekomendasi DPRD Jawa Timur.</p>
    </div>

</div>

</body>
</html>
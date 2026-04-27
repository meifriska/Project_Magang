<?php
session_start();
include '../config/koneksi.php';

// 🔥 PROSES KIRIM ADMIN
if (isset($_POST['balas'])) {

    $id_user_post = $_POST['id_user'] ?? 0;
    $pesan = $_POST['pesan'] ?? '';

    if ($id_user_post != 0 && $pesan != '') {

        mysqli_query($conn, "
            INSERT INTO chat (id_user, pengirim, pesan)
            VALUES ('$id_user_post', 'admin', '$pesan')
        ");

        // 🔥 redirect biar gak double kirim & tetap di user yg sama
        header("Location: chat.php?id_user=".$id_user_post);
        exit;
    }
}

// 🔥 QUERY USER LIST
$qUser = mysqli_query($conn, "
    SELECT DISTINCT u.id_user, u.nama_lengkap
    FROM chat c
    JOIN user u ON c.id_user = u.id_user
");

// 🔥 AMBIL ID USER YANG DIPILIH
$id_user = $_GET['id_user'] ?? 0;

// 🔥 QUERY CHAT
$qChat = mysqli_query($conn, "
    SELECT * FROM chat
    WHERE id_user='$id_user'
    ORDER BY waktu ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Chat Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.container-chat {
    display: flex;
    height: 90vh;
}

/* kiri */
.user-list {
    width: 30%;
    border-right: 1px solid #ddd;
    overflow-y: auto;
}

.user-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.user-item:hover {
    background: #f5f5f5;
}

/* kanan */
.chat-area {
    width: 70%;
    display: flex;
    flex-direction: column;
}

.chat-box {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f5f7fb;
}

.bubble {
    padding: 10px 15px;
    border-radius: 15px;
    margin-bottom: 10px;
    max-width: 60%;
}

.user {
    background: #e4e6eb;
}

.admin {
    background: #4CAF50;
    color: white;
    margin-left: auto;
}

/* input */
.chat-input {
    padding: 10px;
    border-top: 1px solid #ddd;
}
</style>
</head>

<body>

<div class="container-chat">

<!-- 🔥 KIRI (LIST USER) -->
<div class="user-list">

<?php while($u = mysqli_fetch_assoc($qUser)) { ?>

    <div class="user-item"
         onclick="window.location='chat.php?id_user=<?= $u['id_user'] ?>'">

        👤 <?= $u['nama_lengkap'] ?>

    </div>

<?php } ?>

</div>

<!-- 🔥 KANAN (CHAT) -->
<div class="chat-area">

<div class="chat-box">

<?php while($c = mysqli_fetch_assoc($qChat)) { ?>

    <div class="bubble <?= $c['pengirim']=='admin' ? 'admin' : 'user' ?>">
        <?= $c['pesan'] ?>
    </div>

<?php } ?>

</div>

<!-- INPUT -->
<form method="POST" class="chat-input d-flex gap-2">
    <input type="hidden" name="id_user" value="<?= $id_user ?>">
    
    <input type="text" name="pesan" class="form-control" required>

    <button name="balas" class="btn btn-primary">Kirim</button>
</form>
<body>

<a href="index.php" class="btn btn-outline-dark mb-3">
    ⬅ Kembali
</a>
</div>

</div>
<script>
var chatBox = document.querySelector('.chat-box');
chatBox.scrollTop = chatBox.scrollHeight;
</script>
</body>
</html>
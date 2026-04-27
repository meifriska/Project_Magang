<?php
session_start();
include '../config/koneksi.php';

$id_user = $_SESSION['id_user'];

// kirim pesan
if (isset($_POST['kirim'])) {
    $pesan = $_POST['pesan'];

    mysqli_query($conn, "
        INSERT INTO chat (id_user, pengirim, pesan)
        VALUES ('$id_user', 'user', '$pesan')
    ");

    header("Location: chat.php"); // biar gak double kirim
    exit;
}

// ambil chat
$q = mysqli_query($conn, "
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
.chat-box {
    max-width: 800px;
    margin: auto;
    background: #f5f7fb;
    padding: 20px;
    border-radius: 15px;
    height: 500px;
    overflow-y: auto;
}

.bubble {
    padding: 10px 15px;
    border-radius: 15px;
    margin-bottom: 10px;
    max-width: 70%;
}

.user {
    background: #4CAF50;
    color: white;
    margin-left: auto;
    text-align: right;
}

.admin {
    background: #e4e6eb;
    text-align: left;
}

.chat-input {
    max-width: 800px;
    margin: auto;
}
</style>

</head>

<body class="p-4">

<h4 class="text-center mb-4">💬 Chat Admin</h4>

<div class="chat-box">

<?php while($c = mysqli_fetch_assoc($q)) { ?>

    <div class="bubble <?= $c['pengirim'] == 'user' ? 'user' : 'admin' ?>">
        <?= $c['pesan'] ?>
    </div>

<?php } ?>

</div>

<!-- INPUT -->
<form method="POST" class="chat-input mt-3 d-flex gap-2">
    <input type="text" name="pesan" class="form-control" placeholder="Ketik pesan..." required>
    <button type="submit" name="kirim" class="btn btn-primary">Kirim</button>
</form>
<a href="index.php" class="btn btn-outline-dark mb-3">
    ⬅ Kembali
</a>


<!-- AUTO SCROLL -->
<script>
var chatBox = document.querySelector('.chat-box');
chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>
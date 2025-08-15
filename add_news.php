<?php
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'editor') {
    header('Location: login.php'); exit();
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $media_type = $_POST["media_type"];
    $media_file = "";
    if (!empty($_FILES["media_file"]["name"])) {
        $media_file = time() . "_" . $_FILES["media_file"]["name"];
        move_uploaded_file($_FILES["media_file"]["tmp_name"], "uploads/" . $media_file);
    } elseif (!empty($_POST["youtube_link"])) {
        $media_file = $_POST["youtube_link"];
    }
    $stmt = $conn->prepare("INSERT INTO news (title, content, media_type, media_file) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $title, $content, $media_type, $media_file);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Berita</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Tambah Berita</h2>
  <?php if($error): ?><div class="card" style="color:red"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Judul" required><br>
        <textarea name="content" placeholder="Isi berita" required></textarea><br>
        <label>Jenis Media:</label><br>
        <select name="media_type">
        <option value="image">Gambar</option>
        <option value="video">Video</option>
        </select><br>
        <input type="file" name="media_file"><br>
        <small>Untuk video bisa upload file MP4 atau masukkan link YouTube di bawah:</small><br>
      <input type="text" name="youtube_link" placeholder="Link YouTube (opsional)"><br>
    <button type="submit">Simpan</button>
    </form>
</div>
</body>
</html>

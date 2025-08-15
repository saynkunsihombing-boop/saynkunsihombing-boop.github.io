<?php
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'editor') {
    header('Location: login.php'); exit();
}
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: dashboard.php'); exit(); }
$stmt = $conn->prepare('SELECT id, title, content, media_file FROM news WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) { header('Location: dashboard.php'); exit(); }
$news = $res->fetch_assoc();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $imageName = $news['media_file'];
    if ($title === '' || $content === '') {
        $error = 'Judul dan isi berita wajib diisi.';
    } else {
        if (!empty($_FILES['media_file']['name'])) {
            $f = $_FILES['media_file'];
            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg','jpeg','png','gif'];
            if (!in_array(strtolower($ext), $allowed)) {
                $error = 'Tipe file tidak diperbolehkan.';
            } else {
                $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $f['name']);
                move_uploaded_file($f['tmp_name'], __DIR__ . '/uploads/' . $imageName);
            }
        }
        if (!$error) {
            $u = $conn->prepare('UPDATE news SET title=?, content=?, media_file=? WHERE id=?');
            $u->bind_param('sssi', $title, $content, $imageName, $id);
            $u->execute();
            header('Location: dashboard.php'); exit();
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Berita</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Edit Berita</h2>
  <?php if($error): ?><div class="card" style="color:red"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <form method="POST" enctype="multipart/form-data" class="card" style="max-width:720px">
    <input type="text" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>
    <textarea name="content" rows="8" required><?php echo htmlspecialchars($news['content']); ?></textarea>
    <?php if(!empty($news['media_file']) && file_exists(__DIR__ . '/uploads/' . $news['media_file'])): ?>
      <div style="margin-bottom:8px">Gambar saat ini:<br><img src="uploads/<?php echo htmlspecialchars($news['media_file']); ?>" style="max-width:240px;margin-top:6px"></div>
    <?php endif; ?>
    <label>Ganti Gambar (opsional)</label>
    <input type="file" name="media_file" accept="media_file/*">
    <div style="display:flex;gap:8px">
      <button type="submit" class="cta">Simpan Perubahan</button>
      <a href="dashboard.php">Batal</a>
    </div>
  </form>
</div>
</body>
</html>

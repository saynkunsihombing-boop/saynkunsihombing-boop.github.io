<?php
require 'config.php'; // koneksi database

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query berita berdasarkan ID
$stmt = $conn->prepare("SELECT id, title, content, media_file, `date` FROM news WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$berita = $result->fetch_assoc();

// Jika berita tidak ditemukan
if (!$berita) {
    echo "<h2>Berita tidak ditemukan</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Yayasan Jalan Harapan Indonesia</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="container nav">
      <div class="brand">
        <div class="logo">JH</div>
        <div>
          <div style="font-weight:700">Yayasan Jalan Harapan</div>
          <div style="font-size:12px;color:var(--muted)">Indonesia</div>
        </div>
      </div>
      <nav>
        <ul>
          <li><a href="index.php">Beranda</a></li>
          <li><a href="index.php">Tentang</a></li>
          <li><a href="index.php">Program</a></li>
          <li><a href="index.php">Berita</a></li>
          <li><a href="index.php">Kontak</a></li>
        </ul>
      </nav>
      <div style="display:flex;gap:10px;align-items:center">
        <a class="cta" href="login.php">Login Editor</a>
      </div>
    </div>
  </header>
<body>
<main>
    <section style="max-width:900px;margin:auto;padding:20px;">
        <h2><?php echo htmlspecialchars($berita['title']); ?></h2>
        <div style="color:var(--muted);font-size:14px;margin-bottom:20px">
            <?php echo date('d M Y', strtotime($berita['date'])); ?>
        </div>

        <?php if (!empty($berita['media_file']) && file_exists(__DIR__ . '/uploads/' . $berita['media_file'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($berita['media_file']); ?>" 
                 alt="Gambar Berita" 
                 style="width:100%;max-height:400px;object-fit:cover;border-radius:8px;margin-bottom:20px">
        <?php endif; ?>

        <p style="line-height:1.6;font-size:16px;">
            <?php echo nl2br(htmlspecialchars($berita['content'])); ?>
        </p>

        <div style="margin-top:30px;">
            <a href="index.php" style="text-decoration:none;color:var(--primary);">&larr; Kembali ke Beranda</a>
        </div>
    </section>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Yayasan Jalan Harapan Indonesia</p>
</footer>

</body>
</html>

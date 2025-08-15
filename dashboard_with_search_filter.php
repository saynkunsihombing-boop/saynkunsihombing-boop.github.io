<?php
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'editor') {
    header('Location: login.php');
    exit();
}

// Ambil parameter search & filter
$news_search = isset($_GET['news_search']) ? trim($_GET['news_search']) : '';
$news_date   = isset($_GET['news_date']) ? trim($_GET['news_date']) : '';
$feedback_search = isset($_GET['feedback_search']) ? trim($_GET['feedback_search']) : '';
$feedback_date   = isset($_GET['feedback_date']) ? trim($_GET['feedback_date']) : '';

// Query Berita
$news_sql = "SELECT id, title, content, media_file, `date` FROM news WHERE 1";
$params = [];
$types  = "";
if ($news_search !== "") {
    $news_sql .= " AND (title LIKE CONCAT('%', ?, '%') OR content LIKE CONCAT('%', ?, '%'))";
    $params[] = $news_search;
    $params[] = $news_search;
    $types .= "ss";
}
if ($news_date !== "") {
    $news_sql .= " AND DATE_FORMAT(`date`, '%Y-%m') = ?";
    $params[] = $news_date;
    $types .= "s";
}
$news_sql .= " ORDER BY `date` DESC";
$stmt = $conn->prepare($news_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$news = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Query Feedback
$feedback_sql = "SELECT id, name, email, message, date FROM feedback WHERE 1";
$params = [];
$types  = "";
if ($feedback_search !== "") {
    $feedback_sql .= " AND (name LIKE CONCAT('%', ?, '%') OR email LIKE CONCAT('%', ?, '%') OR message LIKE CONCAT('%', ?, '%'))";
    $params[] = $feedback_search;
    $params[] = $feedback_search;
    $params[] = $feedback_search;
    $types .= "sss";
}
if ($feedback_date !== "") {
    $feedback_sql .= " AND DATE_FORMAT(`date`, '%Y-%m') = ?";
    $params[] = $feedback_date;
    $types .= "s";
}
$feedback_sql .= " ORDER BY date DESC";
$stmt2 = $conn->prepare($feedback_sql);
if (!empty($params)) {
    $stmt2->bind_param($types, ...$params);
}
$stmt2->execute();
$feedbacks = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<style>
.search-filter-form {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    align-items: center;
}

.search-filter-form input[type="text"],
.search-filter-form select {
    padding: 4px 8px;
    font-size: 13px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: auto;
}

.search-filter-form button {
    padding: 4px 10px;
    font-size: 13px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.search-filter-form button:hover {
    background: #45a049;
}
</style>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard Editor - Yayasan Jalan Harapan Indonesia</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h1>Dashboard Editor</h1>
    <div>
      <span style="color:var(--muted)">Login sebagai: <?php echo htmlspecialchars($_SESSION['username']); ?></span> | 
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <h2>Berita & Kegiatan</h2>
  <p>
  <div style="margin-top:12px">
    <a class="cta" href="add_news.php">Tambah Berita</a>
  </div>
  </p>
  <form method="GET" class="search-filter-form">
      <input type="text" name="search_news" placeholder="Cari berita..." value="<?php echo htmlspecialchars($_GET['search_news'] ?? '') ?>">
      <select name="filter_month_news">
          <option value="">Semua Bulan</option>
          <?php for ($m = 1; $m <= 12; $m++): ?>
              <option value="<?php echo $m; ?>" <?php if (isset($_GET['filter_month_news']) && $_GET['filter_month_news'] == $m) echo 'selected'; ?>>
                  <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
              </option>
          <?php endfor; ?>
      </select>
      <button type="submit">Cari</button>
  </form>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Judul</th><th>Tanggal</th><th>Konten</th><th>Gambar</th></tr>
    <?php if (empty($news)): ?>
      <tr><td colspan="4">Tidak ada berita.</td></tr>
    <?php else: foreach($news as $n): ?>
      <tr>
        <td><?php echo htmlspecialchars($n['title']); ?></td>
        <td><?php echo htmlspecialchars($n['date']); ?></td>
        <td><?php echo htmlspecialchars(substr($n['content'],0,100)); ?>...</td>
        <td><?php if(!empty($n['media_file']) && file_exists('uploads/'.$n['media_file'])): ?>
          <img src="uploads/<?php echo htmlspecialchars($n['media_file']); ?>" style="width:80px;height:50px;object-fit:cover;">
        <?php endif; ?></td>
      </tr>
    <?php endforeach; endif; ?>
  </table>

  <h2>Feedback Kontak</h2>
  <form method="GET" class="search-filter-form">
    <input type="text" name="search_email" placeholder="Cari email..." value="<?php echo htmlspecialchars($_GET['search_news'] ?? '') ?>">
    <select name="filter_month_feedback">
        <option value="">Semua Bulan</option>
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?php echo $m; ?>" <?php if (isset($_GET['filter_month_feedback']) && $_GET['filter_month_feedback'] == $m) echo 'selected'; ?>>
                <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
            </option>
        <?php endfor; ?>
    </select>
    <button type="submit">Cari</button>
  </form>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr><th>Nama</th><th>Email</th><th>Pesan</th><th>Tanggal</th></tr>
    <?php if (empty($feedbacks)): ?>
      <tr><td colspan="4">Tidak ada feedback.</td></tr>
    <?php else: foreach($feedbacks as $f): ?>
      <tr>
        <td><?php echo htmlspecialchars($f['name']); ?></td>
        <td><?php echo htmlspecialchars($f['email']); ?></td>
        <td><?php echo htmlspecialchars(substr($f['message'],0,100)); ?>...</td>
        <td><?php echo htmlspecialchars($f['date']); ?></td>
      </tr>
    <?php endforeach; endif; ?>
  </table>

</div>
</body>
</html>
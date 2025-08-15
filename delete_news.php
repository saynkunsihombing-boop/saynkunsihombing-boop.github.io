<?php
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'editor') {
    header('Location: login.php'); exit();
}
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    // remove image file if exists
    $stmt = $conn->prepare('SELECT media_file FROM news WHERE id=? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (!empty($row['media_file']) && file_exists(__DIR__ . 'uploads/' . $row['media_file'])) {
            @unlink(__DIR__ . 'uploads/' . $row['media_file']);
        }
    }
    $d = $conn->prepare('DELETE FROM news WHERE id=?');
    $d->bind_param('i', $id);
    $d->execute();
}
header('Location: dashboard.php');
exit();
?>
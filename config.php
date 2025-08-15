<?php
// config.php - koneksi database & inisialisasi tabel sederhana
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'yayasan_db';

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
if ($conn->connect_error) {
    die('Koneksi MySQL gagal: ' . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS `" . $DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
$conn->select_db($DB_NAME);

// Create tables if not exists
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('editor','visitor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InNODB DEFAULT CHARSET=utf8mb4;");

// If no admin exists, create default editor account
$res = $conn->query("SELECT COUNT(*) as cnt FROM users");
if ($res) {
    $r = $res->fetch_assoc();
    if ($r['cnt'] == 0) {
        $default_user = 'editor';
        $default_pass = 'password123'; // default - ganti setelah masuk
        $hash = password_hash($default_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'editor')");
        $stmt->bind_param('ss', $default_user, $hash);
        $stmt->execute();
    }
}
?>
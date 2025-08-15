<?php
include 'config.php'; // file koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($name && $email && $message) {
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            header("Location: index.php?success=1#contact");
            exit();
        } else {
            echo "Gagal menyimpan data: " . $conn->error;
        }
    } else {
        echo "Mohon lengkapi semua field.";
    }
} else {
    echo "Metode pengiriman tidak valid.";
}
?>

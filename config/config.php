<?php
// Mulai session
session_start();

// Konfigurasi database
$db_host = "localhost";      // host database
$db_user = "root";           // username database
$db_pass = "";               // password database
$db_name = "platform_kreatif"; // nama database

// Koneksi ke MySQL
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi umum bisa langsung ditaruh di sini atau pakai middleware.php
// Contoh fungsi cek login role
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

?>

<?php
// Konfigurasi database utama aplikasi
$host     = "localhost";
$username = "root";
$password = "";
$database = "decare_db";

// Membuka koneksi database menggunakan ekstensi MySQLi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Validasi kegagalan koneksi secara instan
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>
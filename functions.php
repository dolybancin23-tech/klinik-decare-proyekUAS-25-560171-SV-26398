<?php
// Hubungkan ke file koneksi database murni terlebih dahulu
require_once 'koneksi.php';
/** @var mysqli $koneksi */

// Aktifkan session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memeriksa apakah pengguna sudah login atau belum
function cekSudahLogin()
{
    return isset($_SESSION['login']) && $_SESSION['login'] === true;
}

// Proteksi Route (Tendang paksa pengunjung jika belum otentikasi)
function wajibLogin()
{
    if (!cekSudahLogin()) {
        header("Location: login.php");
        exit;
    }
}

// Mengelola proses unggah foto profil secara aman
function uploadFotoProfil($file, $target_dir = "uploads/pasien/")
{
    // Membuat direktori folder secara otomatis jika belum tercipta di server
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0775, true);
    }

    $nama_file_asli = basename($file["name"]);
    $ekstensi_file  = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));

    // Memastikan muatan berkas adalah gambar 
    $cek_gambar = getimagesize($file["tmp_name"]);
    if ($cek_gambar === false) {
        return [
            'success' => false,
            'message' => 'Berkas yang diunggah bukan gambar valid!'
        ];
    }

    // Membatasi ukuran berkas maksimal 2MB 
    if ($file["size"] > 2000000) {
        return [
            'success' => false,
            'message' => 'Ukuran berkas terlalu besar! Maksimal adalah 2MB.'
        ];
    }

    // Cek format file gambar
    $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];
    if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
        return [
            'success' => false,
            'message' => 'Format berkas salah! Hanya mengizinkan JPG, JPEG, dan PNG.'
        ];
    }

    // Mengubah nama berkas menjadi hash unik menggunakan uniqid() agar tidak bentrok
    $nama_file_unik = "avatar_" . uniqid() . "." . $ekstensi_file;
    $target_file    = $target_dir . $nama_file_unik;

    // Pindahkan file foto ke folder uploads
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return [
            'success' => true,
            'filename' => $nama_file_unik
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Terjadi kegagalan sistem internal saat memindahkan berkas.'
        ];
    }
}

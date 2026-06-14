<?php
require_once 'functions.php';
/** @var mysqli $koneksi */
wajibLogin();

if ($_SESSION['role'] !== 'pasien') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_reservasi  = (int)$_GET['id'];
    $id_user_login = (int)$_SESSION['user_id'];

    // Update jika id_reservasi cocok dengan id_user milik akun yang sedang login dan status masih Pending
    $query = "UPDATE reservasi r 
              INNER JOIN pasien p ON r.id_pasien = p.id_pasien
              SET r.status_periksa = 'Dibatalkan' 
              WHERE r.id_reservasi = ? AND p.id_user = ? AND r.status_periksa = 'Pending'";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ii", $id_reservasi, $id_user_login);

    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        echo "<script>alert('Jadwal kunjungan dokter berhasil dibatalkan.'); window.location='riwayat_reservasi.php';</script>";
    } else {
        echo "<script>alert('Gagal membatalkan reservasi! Jadwal mungkin sudah diproses admin atau tidak ditemukan.'); window.location='riwayat_reservasi.php';</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    header("Location: riwayat_reservasi.php");
}
exit;

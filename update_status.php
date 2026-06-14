<?php
require_once 'functions.php';
/** @var mysqli $koneksi */
wajibLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_POST['id_reservasi']) && isset($_POST['status_baru'])) {
    $id_reservasi = (int)$_POST['id_reservasi'];
    $status_baru  = $_POST['status_baru'];

    // Update status di database
    $query = "UPDATE reservasi SET status_periksa = ? WHERE id_reservasi = ?";
    $stmt  = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "si", $status_baru, $id_reservasi);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Status reservasi berhasil diperbarui!'); window.location='dashboard_reservasi.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status!'); window.location='dashboard_reservasi.php';</script>";
    }
} else {
    header("Location: dashboard_reservasi.php");
}

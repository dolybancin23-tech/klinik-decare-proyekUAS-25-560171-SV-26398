<?php
require_once 'functions.php';
/** @var mysqli $koneksi */

wajibLogin();
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_pasien = (int)$_GET['id'];

    // Ambil nama file foto lama dulu untuk dihapus dari folder uploads
    $q_foto = mysqli_query($koneksi, "SELECT foto, id_user FROM pasien WHERE id_pasien = $id_pasien LIMIT 1");
    if ($pasien = mysqli_fetch_assoc($q_foto)) {
        $id_user_pasien = $pasien['id_user'];

        // Hapus file fisik foto profil pasien kalau ada
        if (!empty($pasien['foto']) && file_exists("uploads/pasien/" . $pasien['foto'])) {
            unlink("uploads/pasien/" . $pasien['foto']);
        }

        // Hapus akun di tabel user (Otomatis menghapus data di tabel pasien karena ada ON DELETE CASCADE)
        mysqli_query($koneksi, "DELETE FROM user WHERE id_user = $id_user_pasien");

        echo "<script>alert('Rekam medis pasien berhasil dihapus!'); window.location='admin_dashboard.php';</script>";
        exit;
    }
}
header("Location: admin_dashboard.php");
exit;

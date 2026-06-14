<?php
// Mengaktifkan akses ke data session yang sedang berjalan
session_start();

// 1. Menghapus semua variabel instansiasi di dalam $_SESSION
session_unset();

// 2. Menghancurkan paket data session aktif di dalam memori server
session_destroy();

// 3. Mengalihkan secara paksa (redirect) halaman pengguna kembali ke login.php
header("Location: login.php");
exit;
?>
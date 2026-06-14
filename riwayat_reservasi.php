<?php
require_once 'functions.php';
/** @var mysqli $koneksi */
wajibLogin();

if ($_SESSION['role'] !== 'pasien') {
    header("Location: index.php");
    exit;
}

$id_user_login = (int)$_SESSION['user_id'];

// Query kompleks JOIN untuk menarik data reservasi dari semua profil keluarga yang dibuat oleh akun ini
$query = "SELECT r.*, p.nama_lengkap, p.nomor_hp, l.nama_layanan, l.harga, j.hari, j.jam_mulai 
          FROM reservasi r
          INNER JOIN pasien p ON r.id_pasien = p.id_pasien
          INNER JOIN layanan l ON r.id_layanan = l.id_layanan
          INNER JOIN jadwal j ON r.id_jadwal = j.id_jadwal
          WHERE p.id_user = ? 
          ORDER BY r.tanggal_reservasi DESC, r.id_reservasi DESC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user_login);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Reservasi Saya - DeCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-50/20 font-sans min-h-screen p-4 sm:p-8">
    <div class="max-w-[1170px] mx-auto bg-white p-6 sm:p-8 rounded-[32px] border border-blue-100/40 shadow-sm mt-4">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 border-b border-gray-100 pb-6">
            <div>
                <a href="index.php" class="text-xs font-bold text-blue-600 uppercase hover:underline">← Kembali ke Beranda</a>
                <h1 class="text-2xl font-black text-slate-900 mt-1">Jadwal Kunjungan Medis Saya</h1>
                <p class="text-gray-400 text-xs mt-0.5">Pantau status persetujuan aktivitas janji temu dan kelola pembatalan antrean.</p>
            </div>
            <a href="tambah_reservasi.php" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-700 transition-all">+ Buat Reservasi Baru</a>
        </div>

        <div class="overflow-x-auto border border-gray-100 rounded-2xl mb-6 shadow-sm">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Nama Pasien</th>
                        <th class="px-6 py-4">Layanan & Biaya</th>
                        <th class="px-6 py-4">Rencana Tanggal & Jam</th>
                        <th class="px-6 py-4 text-center">Status Berkas</th>
                        <th class="px-6 py-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-slate-900 font-semibold">
                    <?php if (mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic font-medium">Anda belum memiliki riwayat atau rencana jadwal kunjungan dokter.</td>
                        </tr>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-blue-50/10 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-slate-950 font-bold"><?= htmlspecialchars($row['nama_lengkap']) ?></p>
                                <p class="text-[11px] text-gray-400 font-normal mt-0.5"><?= htmlspecialchars($row['nomor_hp']) ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-slate-800"><?= htmlspecialchars($row['nama_layanan']) ?></p>
                                <p class="text-xs text-blue-600 font-normal mt-0.5">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">
                                <p><?= date('d F Y', strtotime($row['tanggal_reservasi'])) ?></p>
                                <p class="text-xs text-gray-400 font-normal mt-0.5">Sesi: <?= substr($row['jam_mulai'], 0, 5) ?> WIB</p>
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                <span class="inline-block px-2.5 py-1 rounded-md font-bold text-[10px] uppercase
                                    <?php
                                    if ($row['status_periksa'] === 'Pending') echo 'bg-yellow-100 text-yellow-700';
                                    elseif ($row['status_periksa'] === 'Disetujui') echo 'bg-blue-100 text-blue-700';
                                    elseif ($row['status_periksa'] === 'Selesai') echo 'bg-green-100 text-green-700';
                                    else echo 'bg-red-100 text-red-700';
                                    ?>">
                                    <?= $row['status_periksa'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                <?php if ($row['status_periksa'] === 'Pending'): ?>
                                    <a href="batalkan_reservasi.php?id=<?= $row['id_reservasi'] ?>" onclick="return confirm('Apakah Anda yakin ingin membatalkan jadwal reservasi medis ini?')" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg font-bold hover:bg-red-100 transition-colors">
                                        Batalkan
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 font-normal italic text-[11px]">Tidak dapat diubah</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
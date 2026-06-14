<?php
require_once 'functions.php';
/** @var mysqli $koneksi */

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$jumlahDataPerHalaman = 5;
$halamanAktif = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

$keyword = "";
if (isset($_GET['search'])) {
    $keyword = htmlspecialchars(trim($_GET['search']));
}

// Ambil total data untuk pagination dengan INNER JOIN ke tabel pasien
$queryCount = "SELECT COUNT(*) as total FROM reservasi 
               INNER JOIN pasien ON reservasi.id_pasien = pasien.id_pasien 
               WHERE pasien.nama_lengkap LIKE ? OR pasien.nomor_hp LIKE ?";
$stmtCount = mysqli_prepare($koneksi, $queryCount);
$searchParam = "%$keyword%";
mysqli_stmt_bind_param($stmtCount, "ss", $searchParam, $searchParam);
mysqli_stmt_execute($stmtCount);
$resCount = mysqli_stmt_get_result($stmtCount);
$dataCount = mysqli_fetch_assoc($resCount);
$totalData = $dataCount['total'];

$jumlahHalaman = ceil($totalData / $jumlahDataPerHalaman);

// Ambil data reservasi lengkap dengan JOIN ke tabel pasien
$queryData = "SELECT reservasi.*, pasien.nama_lengkap, pasien.nomor_hp 
              FROM reservasi 
              INNER JOIN pasien ON reservasi.id_pasien = pasien.id_pasien 
              WHERE pasien.nama_lengkap LIKE ? OR pasien.nomor_hp LIKE ? 
              ORDER BY reservasi.id_reservasi DESC LIMIT ?, ?";
$stmtData = mysqli_prepare($koneksi, $queryData);
mysqli_stmt_bind_param($stmtData, "ssii", $searchParam, $searchParam, $awalData, $jumlahDataPerHalaman);
mysqli_stmt_execute($stmtData);
$reservasi = mysqli_stmt_get_result($stmtData);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Panel Reservasi Pasien DeCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-50/20 font-sans p-4 sm:p-8">
    <div class="max-w-[1170px] mx-auto bg-white p-6 sm:p-8 rounded-[32px] border border-blue-100/40 shadow-sm mt-4">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 border-b border-gray-100 pb-6">
            <div>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Sistem Manajemen Internal</span>
                <h1 class="text-2xl font-black text-slate-900 mt-1">Halo, <?= htmlspecialchars($_SESSION['nama_pengguna']) ?> 👋</h1>
            </div>
            <a href="logout.php" class="bg-red-50 text-red-600 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-red-100 transition-colors">Keluar Sistem</a>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <form action="" method="GET" class="flex gap-x-2 w-full md:w-auto">
                <input type="text" name="search" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari nama pasien atau nomor HP..." class="px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500 text-sm w-full md:w-[320px]">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors">Cari</button>
                <?php if (!empty($keyword)): ?>
                    <a href="dashboard_reservasi.php" class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-200 flex items-center transition-colors">Reset</a>
                <?php endif; ?>
            </form>
            <a href="tambah_reservasi.php" class="w-full md:w-auto text-center bg-green-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-green-700 transition-colors shadow-md shadow-green-100">+ Buat Janji Temu Offline</a>
        </div>

        <div class="overflow-x-auto border border-gray-100 rounded-2xl mb-6 shadow-sm">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Foto Gigi Pasien</th>
                        <th class="px-6 py-4">Nama Pasien</th>
                        <th class="px-6 py-4">Nomor HP</th>
                        <th class="px-6 py-4">Keluhan Utama</th>
                        <th class="px-6 py-4 text-center">Aksi / Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-slate-900 font-semibold">
                    <?php if (mysqli_num_rows($reservasi) == 0): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic font-medium">Data reservasi pasien tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>

                    <?php while ($row = mysqli_fetch_assoc($reservasi)): ?>
                        <tr class="hover:bg-blue-50/10 transition-colors">

                            <td class="px-6 py-4">
                                <?php if (!empty($row['foto_gigi'])): ?>
                                    <img src="uploads/<?= $row['foto_gigi'] ?>" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-sm">
                                <?php else: ?>
                                    <span class="text-xs text-gray-400 italic font-normal bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100">Belum ada foto</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4 font-bold text-slate-950"><?= htmlspecialchars($row['nama_lengkap']) ?></td>

                            <td class="px-6 py-4 text-gray-500 font-normal"><?= htmlspecialchars($row['nomor_hp']) ?></td>

                            <td class="px-6 py-4 text-gray-500 font-normal max-w-[260px] truncate"><?= htmlspecialchars($row['keluhan']) ?></td>

                            <td class="px-6 py-4 text-center text-xs">
                                <span class="inline-block px-2.5 py-1 mb-2 rounded-md font-bold text-[10px] uppercase
                                    <?php
                                    if ($row['status_periksa'] === 'Pending') echo 'bg-yellow-100 text-yellow-700';
                                    elseif ($row['status_periksa'] === 'Disetujui') echo 'bg-blue-100 text-blue-700';
                                    elseif ($row['status_periksa'] === 'Selesai') echo 'bg-green-100 text-green-700';
                                    else echo 'bg-red-100 text-red-700';
                                    ?>">
                                    <?= $row['status_periksa'] ?>
                                </span>
                                <br>
                                <form action="update_status.php" method="POST" class="inline-block">
                                    <input type="hidden" name="id_reservasi" value="<?= $row['id_reservasi'] ?>">
                                    <select name="status_baru" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-slate-700 px-2 py-1 rounded-lg text-[10px] font-bold cursor-pointer hover:bg-gray-100 focus:outline-none">
                                        <option value="" disabled selected>Ubah Status</option>
                                        <option value="Disetujui">Setujui</option>
                                        <option value="Selesai">Selesai</option>
                                        <option value="Dibatalkan">Batalkan</option>
                                        <option value="Ditolak">Tolak</option>
                                    </select>
                                </form>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if ($jumlahHalaman > 1): ?>
            <div class="flex justify-center items-center gap-x-1.5 mt-6 border-t border-gray-50 pt-6">
                <?php if ($halamanAktif > 1): ?>
                    <a href="?halaman=<?= $halamanAktif - 1 ?>&search=<?= urlencode($keyword) ?>" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 text-slate-700 rounded-xl text-xs font-bold transition-colors">Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $jumlahHalaman; $i++): ?>
                    <a href="?halaman=<?= $i ?>&search=<?= urlencode($keyword) ?>" class="px-3 py-2 rounded-xl text-xs font-bold transition-all <?= ($i == $halamanAktif) ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-50 hover:bg-gray-100 text-slate-700' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($halamanAktif < $jumlahHalaman): ?>
                    <a href="?halaman=<?= $halamanAktif + 1 ?>&search=<?= urlencode($keyword) ?>" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 text-slate-700 rounded-xl text-xs font-bold transition-colors">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
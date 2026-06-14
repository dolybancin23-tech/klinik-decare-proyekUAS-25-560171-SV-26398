<?php
require_once 'functions.php';
/** @var mysqli $koneksi */

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$limit  = 5;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search       = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, trim($_GET['search'])) : '';
$search_query = "";

if (!empty($search)) {
    $search_query = "WHERE pasien.nama_lengkap LIKE '%$search%' 
                     OR pasien.nomor_hp LIKE '%$search%' 
                     OR pasien.alamat LIKE '%$search%'";
}

$count_query  = "SELECT COUNT(*) as total FROM pasien $search_query";
$count_result = mysqli_query($koneksi, $count_query);
$total_data   = mysqli_fetch_assoc($count_result)['total'];
$total_pages  = ceil($total_data / $limit);

// inner join menggunakan id_user yang benar dan ORDER BY id_pasien
$query = "SELECT pasien.*, user.email 
          FROM pasien 
          INNER JOIN user ON pasien.id_user = user.id_user 
          $search_query 
          ORDER BY pasien.id_pasien DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - DeCare</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter var', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>

<body class="bg-slate-50/60 font-sans antialiased">
    <header class="pt-4 px-[30px]">
        <nav class="flex justify-between items-center max-w-[1170px] mx-auto h-[72px] px-[30px] bg-white border border-slate-100 rounded-2xl shadow-sm">
            <div class="flex items-center gap-x-2">
                <img src="assets/img/logo.svg" alt="logo" class="h-8 w-auto">
                <span class="hidden md:block px-2 py-0.5 bg-blue-50 text-[9px] font-bold text-blue-600 uppercase rounded-md tracking-wider">Admin Panel</span>
            </div>
            <div class="flex items-center gap-x-4">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-900">Administrator</p>
                    <p class="text-[9px] font-medium text-gray-400">Klinik DeCare</p>
                </div>

                <a href="dashboard_reservasi.php" class="px-3 py-1.5 bg-green-600 text-xs font-bold text-white rounded-xl hover:bg-green-700 transition-all">
                    Lihat Antrean Reservasi
                </a>

                <a href="logout.php" class="px-3 py-1.5 bg-blue-600 text-xs font-bold text-white rounded-xl hover:bg-red-600 transition-all">Logout</a>
            </div>
        </nav>
    </header>

    <main class="max-w-[1170px] mx-auto mt-8 px-[30px] pb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-black text-slate-950">Database Profil Pasien (Buku Induk)</h1>
                <p class="text-xs text-gray-400">Total Terdaftar: <span class="font-bold text-slate-800"><?= $total_data ?></span> Record</p>
            </div>
            <form action="" method="GET" class="flex items-center gap-x-2">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama, kontak, atau alamat..." class="w-full sm:w-[240px] px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                <button type="submit" class="px-4 py-1.5 bg-blue-600 rounded-xl text-xs font-bold text-white shadow-sm hover:bg-blue-700 transition-all">Cari</button>
            </form>
        </div>

        <div class="w-full overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-gray-100 text-[10px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="p-4">Nama Lengkap Pasien</th>
                        <th class="p-4">Kontak Medis</th>
                        <th class="p-4">Alamat Domisili</th>
                        <th class="p-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs text-slate-600">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="p-4 font-bold text-slate-900 flex items-center gap-x-2.5">
                                    <div class="flex items-center justify-center w-7 h-7 font-black text-blue-600 bg-blue-50 border border-blue-100 rounded-full text-[11px] uppercase shrink-0">
                                        <?= substr($row['nama_lengkap'], 0, 1) ?>
                                    </div>
                                    <span><?= htmlspecialchars($row['nama_lengkap']) ?></span>
                                </td>
                                <td class="p-4">
                                    <p class="font-medium text-slate-800"><?= htmlspecialchars($row['email']) ?></p>
                                    <p class="text-[11px] text-gray-400 mt-0.5"><?= htmlspecialchars($row['nomor_hp']) ?></p>
                                </td>
                                <td class="p-4 max-w-[220px] truncate text-gray-500"><?= htmlspecialchars($row['alamat']) ?></td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center gap-x-1.5">
                                        <a href="edit.php?id=<?= $row['id_pasien'] ?>" class="px-2.5 py-1 bg-blue-50 text-blue-600 font-bold rounded-lg hover:bg-blue-100 transition-all">Edit</a>
                                        <a href="hapus.php?id=<?= $row['id_pasien'] ?>" onclick="return confirm('Hapus berkas medis pasien ini?')" class="px-2.5 py-1 bg-red-50 text-red-600 font-bold rounded-lg hover:bg-red-100 transition-all">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400 italic">Data pasien tidak ditemukan atau database kosong.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center items-center gap-x-1 mt-6">
                <a href="?page=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-semibold bg-white text-slate-600 hover:bg-slate-50 <?= ($page <= 1) ? 'pointer-events-none opacity-40' : '' ?>">Prev</a>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="px-3 py-1.5 border <?= ($i === $page) ? 'bg-blue-600 border-blue-600 text-white font-bold' : 'bg-white border-gray-200 text-slate-600 font-medium hover:bg-slate-50' ?> rounded-lg text-xs"><?= $i ?></a>
                <?php endfor; ?>
                <a href="?page=<?= min($total_pages, $page + 1) ?>&search=<?= urlencode($search) ?>" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-semibold bg-white text-slate-600 hover:bg-slate-50 <?= ($page >= $total_pages) ? 'pointer-events-none opacity-40' : '' ?>">Next</a>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>
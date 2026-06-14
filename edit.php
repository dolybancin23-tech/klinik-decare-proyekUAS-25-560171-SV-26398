<?php
require_once 'functions.php';
/** @var mysqli $koneksi */

wajibLogin();
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_pasien = (int)$_GET['id'];
$errors = [];
$success_message = "";

// Query mengambil data profil pasien dan akun user terkait
$query_fetch = "SELECT pasien.*, user.username, user.email 
                FROM pasien 
                INNER JOIN user ON pasien.id_user = user.id_user 
                WHERE pasien.id_pasien = ?";
$stmt_fetch  = mysqli_prepare($koneksi, $query_fetch);
mysqli_stmt_bind_param($stmt_fetch, "i", $id_pasien);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);

if (mysqli_num_rows($result_fetch) === 0) {
    header("Location: index.php");
    exit;
}

$data_lama = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (isset($_POST['update'])) {
    $nama_lengkap = htmlspecialchars(trim($_POST['nama_lengkap']));
    $nomor_hp     = htmlspecialchars(trim($_POST['nomor_hp']));
    $alamat       = htmlspecialchars(trim($_POST['alamat']));

    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap pasien wajib diisi!";
    }

    if (empty($errors)) {
        $query_update = "UPDATE pasien SET nama_lengkap = ?, nomor_hp = ?, alamat = ? WHERE id_pasien = ?";
        $stmt_update  = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt_update, "sssi", $nama_lengkap, $nomor_hp, $alamat, $id_pasien);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            $success_message = "Profil data pasien berhasil diperbarui!";

            $data_lama['nama_lengkap'] = $nama_lengkap;
            $data_lama['nomor_hp'] = $nomor_hp;
            $data_lama['alamat'] = $alamat;
        } else {
            mysqli_stmt_close($stmt_update);
            $errors[] = "Gagal memperbarui data profil pasien ke database!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pasien - DeCare Admin</title>
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

<body class="min-h-screen flex items-center justify-center p-6 bg-slate-50/60">
    <div class="w-full max-w-[600px] p-8 bg-white border border-slate-100 rounded-[24px] shadow-xl">
        <div class="mb-6">
            <a href="admin_dashboard.php" class="text-xs font-bold text-blue-600 hover:underline">← Kembali ke Panel Admin</a>
            <h1 class="text-xl font-black text-slate-950 mt-2">Ubah Profil Pasien</h1>
            <p class="text-xs text-gray-400">Akun: <span class="font-bold text-slate-700"><?= htmlspecialchars($data_lama['username']) ?></span> (<?= htmlspecialchars($data_lama['email']) ?>)</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl text-xs font-medium text-red-600 flex flex-col gap-1">
                <?php foreach ($errors as $err): ?><span>• <?= $err ?></span><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="mb-4 p-3 bg-green-50 border border-green-100 rounded-xl text-xs font-bold text-green-600">
                ✓ <?= $success_message ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="flex flex-col gap-y-4">

            <div class="flex items-center gap-x-4 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                <div class="flex items-center justify-center w-12 h-12 font-black text-blue-600 bg-blue-50 border border-blue-100 rounded-full text-base uppercase shrink-0">
                    <?= substr($data_lama['nama_lengkap'], 0, 1) ?>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">Avatar Pasien</h3>
                </div>
            </div>

            <div>
                <label class="block mb-1.5 text-[10px] font-bold text-slate-700 uppercase">Nama Lengkap Pasien</label>
                <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($data_lama['nama_lengkap']) ?>" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block mb-1.5 text-[10px] font-bold text-slate-700 uppercase">Nomor HP Aktif</label>
                <input type="text" name="nomor_hp" value="<?= htmlspecialchars($data_lama['nomor_hp']) ?>" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block mb-1.5 text-[10px] font-bold text-slate-700 uppercase">Alamat Domisili Rumah</label>
                <textarea name="alamat" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500"><?= htmlspecialchars($data_lama['alamat']) ?></textarea>
            </div>

            <div class="mt-2">
                <button type="submit" name="update" class="w-full cursor-pointer py-2.5 bg-blue-600 border-0 rounded-xl text-xs font-bold text-white shadow-md shadow-blue-100 hover:bg-blue-700 transition-all active:scale-98">Simpan Perubahan Data</button>
            </div>
        </form>
    </div>
</body>

</html>
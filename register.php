<?php
require_once 'functions.php';
/** @var mysqli $koneksi */

// Jika user ternyata sudah melewati gerbang login, langsung lempar ke dashboard utama
if (cekSudahLogin()) {
    header("Location: index.php");
    exit;
}

$errors = [];

// Cek tombol daftar udh diklik atau belum
if (isset($_POST['signup'])) {

    // Menangkap data dari Form Input
    $username     = htmlspecialchars(trim($_POST['username']));
    $email        = htmlspecialchars(trim($_POST['email']));
    $sandi        = trim($_POST['sandi']);
    $nama_lengkap = htmlspecialchars(trim($_POST['nama_lengkap']));
    $nomor_hp     = htmlspecialchars(trim($_POST['nomor_hp']));
    $alamat       = htmlspecialchars(trim($_POST['alamat']));

    // Memvalidasi Aturan Input
    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong!";
    }
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email salah!";
    }
    if (empty($sandi) || strlen($sandi) < 6) {
        $errors[] = "Password minimal harus 6 karakter!";
    }
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap tidak boleh kosong!";
    }
    if (empty($nomor_hp) || !preg_match("/^[0-9]{10,14}$/", $nomor_hp)) {
        $errors[] = "Nomor HP harus berupa angka 10-14 digit!";
    }

    // Mengecek Email atau Username yang sudah dipake
    if (empty($errors)) {
        $queryCek = "SELECT * FROM user WHERE username = ? OR email = ?";
        $stmtCek = mysqli_prepare($koneksi, $queryCek);
        mysqli_stmt_bind_param($stmtCek, "ss", $username, $email);
        mysqli_stmt_execute($stmtCek);
        $resCek = mysqli_stmt_get_result($stmtCek);

        if (mysqli_num_rows($resCek) > 0) {
            $errors[] = "Username atau Email sudah terdaftar!";
        }
        mysqli_stmt_close($stmtCek);
    }

    // Eksekusi Berantai ke Dua Tabel
    if (empty($errors)) {
        $sandiHash = password_hash($sandi, PASSWORD_DEFAULT);

        $queryUser = "INSERT INTO user (username, email, sandi, role) VALUES (?, ?, ?, 'pasien')";
        $stmtUser = mysqli_prepare($koneksi, $queryUser);
        mysqli_stmt_bind_param($stmtUser, "sss", $username, $email, $sandiHash);

        // Ambil id yang otomatis dibuat
        if (mysqli_stmt_execute($stmtUser)) {
            $id_user_baru = mysqli_insert_id($koneksi);
            mysqli_stmt_close($stmtUser);

            // Masukkan data ke tabel pasien
            $queryPasien = "INSERT INTO pasien (id_user, nama_lengkap, nomor_hp, alamat) VALUES (?, ?, ?, ?)";
            $stmtPasien = mysqli_prepare($koneksi, $queryPasien);
            mysqli_stmt_bind_param($stmtPasien, "isss", $id_user_baru, $nama_lengkap, $nomor_hp, $alamat);

            if (mysqli_stmt_execute($stmtPasien)) {
                mysqli_stmt_close($stmtPasien);
                echo "<script>
                alert('Registrasi Berhasil! Silahkan Login'); 
                window.location='login.php'; 
                </script>";
                exit;
            } else {
                mysqli_stmt_close($stmtPasien);
                $errors[] = "Gagal memuat profil data pasien!";
            }
        } else {
            mysqli_stmt_close($stmtUser);
            $errors[] = "Gagal membuat akun login user!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - DeCare</title>

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

<body class="min-h-screen flex items-center justify-center p-[16px] bg-blue-50/30">

    <div class="grid grid-cols-1 md:grid-cols-12 overflow-hidden max-w-[850px] w-full bg-white border border-blue-100/50 rounded-[32px] shadow-xl">

        <div class="md:col-span-5 hidden md:flex flex-col justify-center p-10 bg-blue-50/60 border-r border-blue-100/30">
            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">
                Registrasi
            </span>

            <h2 class="mt-1 mb-2 text-xl font-black text-slate-900">
                Satu Akun Untuk Keluarga
            </h2>

            <p class="text-xs text-slate-500 leading-relaxed">
                Cukup buat satu akun utama untuk mendaftarkan diri Anda, anak-anak, atau orang tua secara praktis.
            </p>
        </div>

        <div class="md:col-span-7 p-8">
            <h1 class="mb-1 text-lg font-black text-slate-950">
                Buat Akun Baru
            </h1>

            <p class="mb-4 text-xs text-gray-400">
                Lengkapi data akun login dan profil data diri anda
            </p>

            <?php if (!empty($errors)): ?>
                <div class="flex flex-col gap-1 p-3 bg-red-50 border border-red-100 rounded-xl text-xs font-medium text-red-600">
                    <?php foreach ($errors as $err): ?>
                        <span>* <?= $err ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2 pb-1 border-b border-gray-100">
                    <span class="text-xs font-bold text-blue-600 uppercase">
                        1. Kredensial Akun
                    </span>
                </div>

                <div>
                    <label class="block mb-1 text-[10px] font-bold text-slate-700 uppercase">
                        Username
                    </label>
                    <input type="text" name="username" placeholder="Username akun" class="w-full px-3 py-1.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block mb-1 text-[10px] font-bold text-slate-700 uppercase">
                        Email
                    </label>
                    <input type="email" name="email" placeholder="contoh@gmail.com" class="w-full px-3 py-1.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block mb-1 text-[10px] font-bold text-slate-700 uppercase">
                        Password
                    </label>
                    <input type="password" name="sandi" placeholder="Minimal harus 6 karakter" class="w-full px-3 py-1.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>

                <div class="sm:col-span-2 mt-2 pb-1 border-b border-gray-100">
                    <span class="text-xs font-bold text-blue-600 uppercase">
                        2. Profil Pasien Utama
                    </span>
                </div>

                <div>
                    <label class="block mb-1 text-[10px] font-bold text-slate-700 uppercase">
                        Nama Lengkap
                    </label>
                    <input type="text" name="nama_lengkap" placeholder="Sesuai KTP" class="w-full px-3 py-1.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block mb-1 text-[10px] font-bold text-slate-700 uppercase">
                        Nomor HP
                    </label>
                    <input type="text" name="nomor_hp" placeholder="08xxxxxxxxxx" class="w-full px-3 py-1.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block mb-1 text-[10px] font-bold text-slate-700 uppercase">
                        Alamat Rumah
                    </label>
                    <textarea name="alamat" rows="2" placeholder="Alamat tinggal sekarang..." class="w-full px-3 py-1.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="sm:col-span-2 mt-3">
                    <button type="submit" name="signup" class="w-full cursor-pointer py-2.5 bg-blue-600 border-0 rounded-xl text-xs font-bold text-white shadow-md shadow-blue-100 hover:bg-blue-700 transition-all active:scale-98">
                        Daftar Akun DeCare
                    </button>
                </div>
            </form>

            <p class="text-center mt-4 text-xs text-gray-400">
                Sudah memiliki akun?
                <a href="login.php" class="text-blue-600 font-bold hover:underline">
                    Login disini
                </a>
            </p>
        </div>
    </div>

</body>

</html>
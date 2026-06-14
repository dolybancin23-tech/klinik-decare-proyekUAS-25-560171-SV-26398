<?php
require_once 'functions.php';
/** @var mysqli $koneksi */ 

if (cekSudahLogin()) {
    header("Location: index.php");
    exit;
}

$error_message = "";

if (isset($_POST['login'])) {
    $username_input = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password_input = $_POST['password'];

    if (!empty($username_input) && !empty($password_input)) {
        $query  = "SELECT * FROM user WHERE username = '$username_input' OR email = '$username_input'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) === 1) {
            $user_data = mysqli_fetch_assoc($result);

            if (password_verify($password_input, $user_data['sandi'])) {
                
                $_SESSION['login']     = true;
                $_SESSION['user_id']   = $user_data['id_user']; // FIXED: id -> id_user
                $_SESSION['username']  = $user_data['username'];
                $_SESSION['role']      = $user_data['role']; 

                if ($user_data['role'] === 'pasien') {
                    $id_user = $user_data['id_user']; // FIXED: id -> id_user
                    
                    $query_pasien = "SELECT nama_lengkap FROM pasien WHERE id_user = '$id_user' LIMIT 1";
                    $res_pasien   = mysqli_query($koneksi, $query_pasien);
                    
                    if (mysqli_num_rows($res_pasien) === 1) {
                        $pasien_data = mysqli_fetch_assoc($res_pasien);
                        $_SESSION['nama_pengguna'] = $pasien_data['nama_lengkap'];
                    } else {
                        $_SESSION['nama_pengguna'] = $user_data['username'];
                    }
                } else {
                    $_SESSION['nama_pengguna'] = "Administrator";
                }

                header("Location: index.php");
                exit;

            } else {
                $error_message = "Password yang Anda masukkan salah!";
            }
        } else {
            $error_message = "Username atau Email tidak terdaftar!";
        }
    } else {
        $error_message = "Semua kolom form wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DeCare</title>
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-blue-50/30">
    <div class="w-full max-w-[420px] overflow-hidden p-8 bg-white border border-blue-100/50 rounded-[32px] shadow-xl">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-black text-slate-950 tracking-tight">Selamat Datang</h1>
            <p class="mt-1 text-xs text-gray-400">Masuk ke Sistem Aplikasi Klinik Gigi DeCare</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="p-3 bg-red-50 border border-red-100 rounded-xl text-xs font-medium text-red-600">
                • <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="flex flex-col gap-y-4 mt-4">
            <div>
                <label class="block mb-1.5 text-[10px] font-bold text-slate-700 uppercase tracking-wide">Username atau Email</label>
                <input type="text" name="username" placeholder="Masukkan akun Anda" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 transition-all">
            </div>
            <div>
                <label class="block mb-1.5 text-[10px] font-bold text-slate-700 uppercase tracking-wide">Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 transition-all">
            </div>
            <button type="submit" name="login" class="w-full cursor-pointer mt-2 py-2.5 bg-blue-600 border-0 rounded-xl text-xs font-bold text-white shadow-md shadow-blue-100 hover:bg-blue-700 transition-all active:scale-98">Masuk ke Akun</button>
        </form>

        <p class="text-center mt-6 text-xs text-gray-400">Belum memiliki akun? <a href="register.php" class="text-blue-600 font-bold hover:underline">Daftar sekarang</a></p>
    </div>
</body>
</html>
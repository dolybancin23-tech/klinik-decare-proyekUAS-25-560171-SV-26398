<?php
// 1. Ambil kendali utilitas sistem (Koneksi database & Session otomatis aktif)
require_once 'functions.php';
/** @var mysqli $koneksi */

// 2. jika yang login admin -> Alihkan ke panel admin 
if (isset($_SESSION['login']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

// 3. ambil data layanan dari databse
$query_layanan = "SELECT * FROM layanan ORDER BY id_layanan ASC";
$result_layanan = mysqli_query($koneksi, $query_layanan);

// 4. Mapping data statis
$info_layanan = [
    1 => [
        'icon' => 'icon-pemutihan.svg',
        'desc' => 'Tak pernah mudah untuk mencerahkan senyummu. Tersedia beberapa jenis produk.',
        'modal_desc' => 'Tak Pernah Mudah Untuk Mencerahkan Senyummu. Prosedur pemutihan gigi DeCare menggunakan bahan pencerah khusus yang aman untuk enamel, efektif menghilangkan noda kopi, teh, atau rokok, sehingga gigi terlihat beberapa tingkat lebih cerah secara instan.',
        'durasi' => '60 Menit / Kunjungan'
    ],
    2 => [
        'icon' => 'icon-salkar.svg',
        'desc' => 'Prosedur gigi yang digunakan untuk mengobati infeksi yang terjadi di pusat gigi.',
        'modal_desc' => 'Layanan spesialis untuk menyelamatkan gigi asli yang terinfeksi parah. Tim bedah mulut DeCare akan membersihkan saluran akar yang terinfeksi, menghilangkan saraf yang sakit, dan menambal secara permanen agar gigi dapat digunakan kembali.',
        'durasi' => '90-120 Menit (2 Kunjungan)'
    ],
    3 => [
        'icon' => 'icon-darurat.svg',
        'desc' => 'Masalah gigi yang membutuhkan perawatan segera, pendarahan, mengurangi rasa sakit, dll.',
        'modal_desc' => 'Penanganan cepat untuk masalah mendesak seperti sakit gigi parah, gigi patah akibat benturan, pendarahan gusi tidak terkendali, atau abses. Layanan darurat ini fokus untuk segera menghilangkan rasa sakit dan menstabilkan kondisi mulut Anda.',
        'durasi' => '30-60 Menit (Sesuai Tindakan)'
    ],
    4 => [
        'icon' => 'icon-cosmetic.svg',
        'desc' => 'Layanan kami ini berfokus untuk meningkatan penampilan senyuman Anda.',
        'modal_desc' => 'Prosedur untuk meningkatkan senyummu (Veneer, Crowning). Kami memperbaiki bentuk, ukuran, dan warna gigi agar terlihat rata, putih, dan sempurna. Dapatkan senyum "Hollywood Smile" yang percaya diri bersama DeCare.',
        'durasi' => '90 Menit per gigi'
    ],
    5 => [
        'icon' => 'icon-implan.svg',
        'desc' => 'Akar gigi buatan yang ditempatkan di rahang Anda untuk menahan gigi prostetik.',
        'modal_desc' => 'Solusi paling permanen untuk mengganti gigi hilang. Akar buatan dari titanium ditempatkan di rahang oleh ahli bedah kami untuk menahan gigi prostetik yang tampak alami, kuat, dan fungsional seperti gigi asli.',
        'durasi' => '6-9 Bulan Total'
    ],
    6 => [
        'icon' => 'icon-pencegahan.svg',
        'desc' => 'Perawatan gigi yang membantu menjaga kesehatan mulut melalui perawatan rutin.',
        'modal_desc' => 'Perawatan mendasar (Scaling, Checkup) untuk menjaga kesehatan mulut murni. Pembersihan karang gigi mendalam dan pemeriksaan rutin DeCare membantu mencegah karies gigi dan penyakit gusi secara efektif.',
        'durasi' => '45 Menit / 6 Bulan'
    ]
];
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeCare - Klinik Gigi Digital</title>

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

<body class="bg-white font-sans antialiased">

    <header class="sticky top-0 z-50 w-full pt-4 px-[30px] bg-transparent">

        <nav class="flex justify-between items-center relative max-w-[1170px] mx-auto h-[72px] px-[30px] bg-sky-100 backdrop-blur-md border border-blue-100/50 rounded-2xl shadow-sm">

            <div class="flex items-center">
                <img src="assets/img/logo.svg" alt="logo" class="h-10 w-auto">
            </div>

            <ul class="hidden md:flex items-center gap-x-[30px]">
                <li><a href="#beranda" class="text-gray-950 font-semibold hover:text-blue-600 transition-colors">Beranda</a></li>
                <li><a href="#layanan" class="text-gray-500 font-medium hover:text-blue-600 transition-colors">Layanan</a></li>
                <li><a href="#operasional" class="text-gray-500 font-medium hover:text-blue-600 transition-colors">Operasional</a></li>
                <li><a href="#tentang-kami" class="text-gray-500 font-medium hover:text-blue-600 transition-colors">Tentang Kami</a></li>
            </ul>

            <div class="hidden sm:flex items-center gap-x-[30px]">
                <?php if (isset($_SESSION['login']) && $_SESSION['role'] === 'pasien'): ?>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-950"><?= htmlspecialchars($_SESSION['nama_pengguna']); ?></p>
                        <p class="text-[9px] font-bold text-blue-600 uppercase tracking-wider">Pasien DeCare</p>
                    </div>

                    <a href="riwayat_reservasi.php" class="px-4 py-2 border-2 border-[#1376F8] text-[#1376F8] rounded-xl text-xs font-bold hover:bg-blue-50 transition-all">
                        Jadwal Saya
                    </a>

                    <a href="logout.php" class="px-5 py-2.5 bg-[#1376F8] rounded-xl text-xs font-bold text-white hover:bg-red-700 transition-all">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="border-b-2 border-blue-600 pb-0.5 text-blue-600 font-bold hover:text-blue-800 transition-all">
                        Masuk
                    </a>
                    <a href="register.php" class="px-6 py-3 bg-[#1376F8] rounded-xl text-sm font-bold text-white shadow-md shadow-blue-100 hover:bg-blue-800 transition-all">
                        Reservasi
                    </a>
                <?php endif; ?>
            </div>

            <button id="menu-toggle" class="flex md:hidden flex-col justify-between cursor-pointer focus:outline-none group w-6 h-[20px] bg-transparent border-0 text-gray-900" aria-label="Buka Menu">
                <span class="w-full h-[2px] bg-gray-900 rounded transition-all duration-300"></span>
                <span class="w-full h-[2px] bg-gray-900 rounded transition-all duration-300"></span>
                <span class="w-full h-[2px] bg-gray-900 rounded transition-all duration-300"></span>
            </button>
        </nav>
        <div id="mobile-overlay" class="hidden fixed inset-0 z-60 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0 duration-300"></div>

        <div id="mobile-menu" class="hidden fixed top-0 right-0 bottom-0 z-[70] flex-col w-[85%] max-w-[320px] bg-white shadow-[0_0_40px_rgba(0,0,0,0.1)] translate-x-full transition-transform duration-300 overflow-y-auto pb-10">

            <div class="flex items-center justify-between px-6 py-[20px] border-b border-gray-100 shrink-0">
                <img src="assets/img/logo.svg" alt="DeCare" class="h-7 w-auto">
                <button id="menu-close" class="cursor-pointer focus:outline-none p-2 -mr-2 bg-slate-50 hover:bg-red-50 rounded-full text-slate-500 hover:text-red-500 transition-colors" aria-label="Tutup Menu">
                    <img src="assets/img/icon-mobile.svg" alt="Menu mobile" class="w-4 h-4 shrink-0 object-contain">
                </button>
            </div>

            <div class="px-6 py-[30px]">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-4 pl-3">Navigasi Utama</p>
                <ul class="flex flex-col gap-y-4 text-left font-semibold text-slate-700 mb-8">
                    <li><a href="#beranda" class="block border-l-[3px] border-transparent py-2 pl-[19px] hover:text-blue-600 hover:bg-slate-50 rounded-r-lg transition-colors">Beranda</a></li>
                    <li><a href="#layanan" class="block border-l-[3px] border-transparent py-2 pl-[19px] hover:text-blue-600 hover:bg-slate-50 rounded-r-lg transition-colors">Layanan</a></li>
                    <li><a href="#operasional" class="block border-l-[3px] border-transparent py-2 pl-[19px] hover:text-blue-600 hover:bg-slate-50 rounded-r-lg transition-colors">Operasional</a></li>
                    <li><a href="#tentang-kami" class="block border-l-[3px] border-transparent py-2 pl-[19px] hover:text-blue-600 hover:bg-slate-50 rounded-r-lg transition-colors">Tentang Kami</a></li>
                </ul>

                <div class="pt-6 border-t border-gray-100">
                    <div class="flex flex-col gap-y-3">
                        <?php if (isset($_SESSION['login'])): ?>

                            <a href="riwayat_reservasi.php" class="w-full py-[14px] bg-white border-2 border-[#1376F8] rounded-xl text-center font-bold text-[#1376F8] hover:bg-blue-50 transition-colors active:scale-95">Reservasi Saya</a>

                            <a href="logout.php" class="w-full py-[14px] bg-[#1376F8] rounded-xl text-center font-bold text-white shadow-md hover:bg-red-600 transition-colors active:scale-95">Logout dari Akun</a>
                        <?php else: ?>
                            <a href="login.php" class="w-full py-[14px] bg-white border-2 border-[#1376F8] rounded-xl text-center font-bold text-[#1376F8] hover:bg-blue-50 transition-colors active:scale-95">Masuk Akun</a>
                            <a href="register.php" class="w-full py-[14px] bg-[#1376F8] rounded-xl text-center font-bold text-white shadow-md shadow-blue-200 hover:bg-blue-700 transition-colors active:scale-95">Registrasi Pasien Baru</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

    </header>

    <main>
        <section id="beranda" class="w-full relative overflow-visible py-12 lg:py-20 bg-white">
            <div class="max-w-[1170px] mx-auto px-[30px]">
                <div class="grid grid-cols-1 lg:grid-cols-2 relative z-20 items-center gap-[48px] w-full">
                    <div class="flex flex-col items-center lg:items-start lg:-mt-[16px] w-full lg:max-w-[570px] text-center lg:text-left">
                        <h1 class="w-full mb-6 text-[40px] lg:text-[60px] font-black text-gray-950 leading-[1.1] tracking-[-1px]">
                            <?php if (isset($_SESSION['login']) && $_SESSION['role'] === 'pasien'): ?>
                                Selamat Datang,<br><span class="text-gray-950"><?= htmlspecialchars($_SESSION['nama_pengguna']); ?></span>
                            <?php else: ?>
                                Kami Menawarkan <br class="hidden lg:block"> Pelayanan Terbaik!
                            <?php endif; ?>
                        </h1>

                        <p class="max-w-[480px] mb-8 text-[16px] lg:text-[18px] text-gray-500 leading-[1.6]">
                            Kami hanya menggunakan bahan berkualitas terbaik di pasaran untuk menyediakan produk terbaik kepada pasien kami, jadi jangan khawatir tentang apa pun.
                        </p>

                        <div class="flex flex-col sm:flex-row items-center gap-6 w-full sm:w-auto">
                            <a href="<?= isset($_SESSION['login']) ? 'tambah_reservasi.php' : 'login.php' ?>" class="w-full sm:w-auto px-8 py-[16px] bg-[#1376F8] rounded-xl text-center font-bold text-white shadow-lg shadow-blue-100 hover:bg-blue-800 active:scale-95 transition-all whitespace-nowrap">
                                Reservasi Janji Temu
                            </a>

                            <div class="flex items-center shrink-0 gap-x-[12px]">
                                <div class="flex items-center justify-center w-[48px] h-[48px] bg-blue-50 border border-blue-100 rounded-xl text-blue-600">
                                    <img src="assets/img/icon-telp.svg" alt="telepon" class="h-[21px] w-auto">
                                </div>
                                <div class="text-left">
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Layanan 24 Jam</p>
                                    <p class="text-[14px] font-bold text-gray-950 whitespace-nowrap">0813330121</p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full max-w-[360px] mt-12 mb-2 p-4 bg-gray-50/80 border border-gray-100 rounded-2xl text-left shadow-sm">
                            <div class="flex items-center gap-x-3">
                                <div class="w-11 h-11 rounded-full overflow-hidden bg-gray-200">
                                    <img src="assets/img/thomas.png" alt="Thomas Setiawan" class="w-full h-full object-cover" onerror="this.style.display='none'">
                                </div>
                                <div>
                                    <h4 class="text-[14px] font-bold text-gray-950">Thomas Setiawan</h4>
                                    <p class="text-[10px] font-medium text-gray-400">Ahli Bedah Mulut</p>
                                </div>
                            </div>
                            <p class="mt-2 text-[13px] text-gray-500 leading-relaxed italic">
                                "Perawatan berkualitas tinggi dilakukan oleh para ahli di bidangnya, Sangat recommended"
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-center lg:justify-end items-start relative w-full mt-8 lg:mt-0 lg:h-[520px]">
                        <img src="assets/img/gambar-heroSection.png" alt="Dokter DeCare & Ornamen Gigi" class="z-20 lg:absolute lg:right-[-20px] lg:top-[-115px] object-contain lg:object-right-top w-[90%] sm:w-[80%] lg:w-[640px] h-auto lg:max-w-none">
                    </div>

                </div>
            </div>


            <div class="m-auto max-w-[1170px] px-[30px] lg:px-[60px] py-[40px] lg:mt-18 mb-12 lg:mb-16 bg-sky-200/40 rounded-2xl shadow-sm">

                <div class="flex flex-col lg:flex-row justify-center items-center gap-10 lg:gap-20 text-left">

                    <div class="w-full sm:w-100 lg:w-[450px] h-[350px] lg:h-[450px] rounded-[24px] overflow-hidden shadow-md shrink-0">
                        <img src="assets/img/gambar-beranda3.png" alt="Dokter sedang bekerja" class="w-full h-full object-cover">
                    </div>

                    <div class="w-full lg:max-w-[480px]">
                        <h2 class="text-[32px] lg:text-[40px] font-black text-slate-900 leading-[1.2] mb-5 tracking-tight">
                            Kenapa Harus
                            <span class="relative inline-block text-slate-900 z-10">Memilih
                                <img src="assets/img/garis-abstrak.svg" alt="garis dekorasi" class="absolute -bottom-2 left-0 w-full h-auto -z-10">
                            </span><br> Kami Untuk Perawatan
                        </h2>
                        <p class="text-[14px] lg:text-[15px] text-gray-500 leading-[1.6] mb-8">
                            Kami hanya menggunakan bahan berkualitas terbaik di pasaran untuk memberikan produk terbaik kepada pasien.
                        </p>

                        <ul class="flex flex-col gap-y-4 mb-8">
                            <li class="flex items-center gap-x-3 text-[14px] lg:text-[15px] font-bold text-slate-700">
                                <img src="assets/img/icon-centang.svg" alt="Check" class="w-5 h-5 shrink-0 object-contain">
                                Tim perawatan terbaik
                            </li>
                            <li class="flex items-center gap-x-3 text-[14px] lg:text-[15px] font-bold text-slate-700">
                                <img src="assets/img/icon-centang.svg" alt="Check" class="w-5 h-5 shrink-0 object-contain">
                                Layanan gigi terbaru
                            </li>
                            <li class="flex items-center gap-x-3 text-[14px] lg:text-[15px] font-bold text-slate-700">
                                <img src="assets/img/icon-centang.svg" alt="Check" class="w-5 h-5 shrink-0 object-contain">
                                Diskon di setiap perawatan
                            </li>
                            <li class="flex items-center gap-x-3 text-[14px] lg:text-[15px] font-bold text-slate-700">
                                <img src="assets/img/icon-centang.svg" alt="Check" class="w-5 h-5 shrink-0 object-contain">
                                Reservasi cepat dan mudah
                            </li>
                        </ul>

                        <a href="<?= isset($_SESSION['login']) ? 'tambah_reservasi.php' : 'login.php' ?>" class="inline-block px-[28px] py-[14px] bg-[#1376F8] rounded-xl text-sm font-bold text-white shadow-md hover:bg-blue-700 transition-all active:scale-95">
                            Reservasi Janji Temu
                        </a>
                    </div>
                </div>
            </div>

            <div class="max-w-[1170px] mx-auto px-[30px] lg:px-[60px]">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-[60px] items-center text-left">

                    <div class="order-2 lg:order-1">
                        <h2 class="text-[32px] lg:text-[40px] font-black text-slate-900 leading-[1.2] mb-6">
                            Tinggalkan Kekhawatiran Anda Dan Nikmati Senyum Yang <span class="border-b-[4px] border-blue-400 pb-0.5">Lebih Sehat Dan Rapi</span>
                        </h2>
                        <p class="text-[14px] text-gray-500 leading-[1.6] mb-8 max-w-[460px]">
                            Kami hanya menggunkan bahan berkualitas terbaik di pasaran untuk menyediakan produk terbaik kepada pasien kami, jadi jangan khawatir tentang apa pun.
                        </p>

                        <a href="<?= isset($_SESSION['login']) ? 'tambah_reservasi.php' : 'login.php' ?>" class="inline-block px-6 py-[12px] bg-[#1376F8] rounded-lg text-sm font-bold text-white shadow-md hover:bg-blue-700 transition-all active:scale-95">
                            Reservasi Janji Temu
                        </a>
                    </div>

                    <div class="order-1 lg:order-2 w-full flex justify-center lg:justify-end">
                        <img src="assets/img/pasien-senyum-grup.png" alt="Senyum Sehat" class="w-full lg:w-[90%] h-auto object-contain">
                    </div>

                </div>
            </div>


        </section>

        <section id="layanan" class="w-full relative z-10 py-12 lg:py-20 bg-gradient-to-b from-[#FEFFFF] via-[#E7F6FE] to-[#E7F6FE]">
            <div class="max-w-[1170px] mx-auto text-center px-[30px] lg:px-[60px]">

                <div class="mb-14 flex flex-col items-center">
                    <h2 class="text-[36px] lg:text-[44px] font-black text-slate-900 mb-5 relative inline-block tracking-tight z-10">
                        Layanan
                        <img src="assets/img/garis-abstrak2.svg" alt="garis dekorasi" class="absolute -bottom-2 left-0 w-full h-auto -z-10">
                    </h2>
                    <p class="max-w-[500px] text-[14px] text-gray-500 leading-relaxed">
                        Kami hanya menggunakan bahan berkualitas terbaik di pasaran untuk memberikan produk terbaik kepada pasien.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php
                    // Loop data layanan dari database
                    while ($layanan = mysqli_fetch_assoc($result_layanan)):
                        $id = $layanan['id_layanan'];

                        // Tarik info statis dari array mapping di atas, gunakan default jika tidak ketemu
                        $icon = isset($info_layanan[$id]['icon']) ? $info_layanan[$id]['icon'] : 'icon-pencegahan.svg';
                        $desc = isset($info_layanan[$id]['desc']) ? $info_layanan[$id]['desc'] : 'Deskripsi layanan perawatan DeCare.';
                    ?>
                        <div class="flex flex-col items-center text-center p-10 bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg transition-all h-full">
                            <div class="flex items-center justify-center mb-6 w-16 h-16 shrink-0 rounded-full bg-[#1da1f2]">
                                <img src="assets/img/<?= $icon ?>" alt="<?= htmlspecialchars($layanan['nama_layanan']) ?>" class="w-8 h-8 object-contain">
                            </div>

                            <h3 class="mb-3 text-[18px] font-bold text-slate-900">
                                <?= htmlspecialchars($layanan['nama_layanan']) ?>
                            </h3>

                            <p class="mb-8 text-[13px] text-gray-500 leading-relaxed"><?= $desc ?></p>

                            <button onclick="openModal(<?= $id ?>)" class="mt-auto flex items-center gap-x-1.5 text-slate-900 hover:text-blue-600 transition-colors group cursor-pointer bg-transparent border-none">
                                <span class="text-[12px] font-bold border-b border-slate-900 group-hover:border-blue-600 transition-colors pb-0.5">Pelajari Lebih Lanjut</span>
                                <img src="assets/img/icon-panah.svg" alt="Panah" class="w-4 h-4 shrink-0 object-contain transition-transform group-hover:translate-x-1">
                            </button>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <section id="operasional" class="w-full pt-12 lg:pt-20 bg-[#E7F6FE]">
            <div class="max-w-[1170px] mx-auto px-[30px] lg:px-[60px]">
                <div class="mb-14 flex flex-col items-center text-center">
                    <h2 class="text-[36px] lg:text-[44px] font-black text-slate-900 mb-5 relative inline-block tracking-tight z-10">
                        Informasi Operasional
                        <img src="assets/img/garis-abstrak2.svg" alt="garis dekorasi" class="absolute -bottom-2 left-0 w-full h-auto -z-10">
                    </h2>
                    <p class="max-w-[550px] text-[14px] text-gray-500 leading-relaxed">
                        Kunjungi klinik kami pada jam operasional resmi untuk mendapatkan perawatan terbaik dari tim dokter gigi ahli kami.
                    </p>
                </div>

                <div class="w-full bg-[#011632] rounded-[32px] p-6 sm:p-8 lg:p-12 shadow-xl">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                        <div class="lg:col-span-5 w-full h-[320px] sm:h-100 lg:h-[460px] rounded-[24px] overflow-hidden shadow-lg">
                            <img src="assets/img/gambar1-operasional.png" alt="Dokter DeCare sedang bekerja" class="w-full h-full object-cover">
                        </div>

                        <div class="lg:col-span-7 flex flex-col gap-y-4 w-full">

                            <div class="w-full bg-white rounded-[20px] p-5 flex items-start sm:items-center gap-x-5 shadow-sm border border-gray-100/50 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-center w-12 h-12 shrink-0 rounded-full bg-[#1376F8] text-white shadow-md shadow-blue-100">
                                    <img src="assets/img/icon-jam.svg" alt="Jam Operasional" class="w-6 h-6 object-contain">
                                </div>
                                <div class="text-left">
                                    <h4 class="text-[15px] font-bold text-slate-900 mb-0.5">Jam Operasional</h4>
                                    <p class="text-[13px] text-gray-600 font-medium leading-relaxed">Senin - Sabtu (10.00 - 17.00)</p>
                                    <p class="text-[13px] text-red-500 font-semibold leading-relaxed">Minggu dan Hari Libur Nasional (Tutup)</p>
                                </div>
                            </div>

                            <div class="w-full bg-white rounded-[20px] p-5 flex items-start sm:items-center gap-x-5 shadow-sm border border-gray-100/50 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-center w-12 h-12 shrink-0 rounded-full bg-[#1376F8] text-white shadow-md shadow-blue-100">
                                    <img src="assets/img/icon-maps.svg" alt="Alamat" class="w-6 h-6 object-contain">
                                </div>
                                <div class="text-left">
                                    <h4 class="text-[15px] font-bold text-slate-900 mb-0.5">Alamat</h4>
                                    <p class="text-[13px] text-gray-600 font-medium leading-relaxed">
                                        Jl. Sunset Road No. 88X, Seminyak, Kuta, Kabupaten Badung, Bali 80361.
                                    </p>
                                </div>
                            </div>

                            <div class="w-full bg-white rounded-[20px] p-5 flex items-start sm:items-center gap-x-5 shadow-sm border border-gray-100/50 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-center w-12 h-12 shrink-0 rounded-full bg-[#1376F8] text-white shadow-md shadow-blue-100">
                                    <img src="assets/img/icon-email.svg" alt="Email" class="w-6 h-6 object-contain">
                                </div>
                                <div class="text-left">
                                    <h4 class="text-[15px] font-bold text-slate-900 mb-0.5">Alamat Email</h4>
                                    <a href="mailto:decare31@gmail.com" class="text-[13px] text-blue-600 font-semibold hover:underline">decare31@gmail.com</a>
                                </div>
                            </div>

                            <div class="w-full bg-white mb-20 rounded-[20px] p-5 flex items-start sm:items-center gap-x-5 shadow-sm border border-gray-100/50 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-center w-12 h-12 shrink-0 rounded-full bg-[#1376F8] text-white shadow-md shadow-blue-100">
                                    <img src="assets/img/icon-telpPutih.svg" alt="Email" class="w-6 h-6 object-contain">
                                </div>
                                <div class="text-left">
                                    <h4 class="text-[15px] font-bold text-slate-900 mb-0.5">Nomor WA/Telepon</h4>
                                    <a href="https://wa.me/0822345613" target="_blank" class="text-[13px] text-slate-700 font-bold hover:text-blue-600 transition-colors">0822345613</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>


            <div class="w-full z-20 mx-auto pt-16 pb-24 lg:pt-24 lg:pb-32 bg-gradient-to-b from-[#E7F6FE] via-[#FEFFFF] to-[#FEFFFF]">
                <div class="max-w-[1170px] mx-auto px-[30px] lg:px-[60px]">

                    <div class="mb-16 flex flex-col items-center text-center">
                        <h2 class="text-[32px] lg:text-[40px] font-black text-slate-900 mb-5 relative inline-block tracking-tight z-10">
                            Kenali Dokter Ahli Kami
                            <img src="assets/img/garis-abstrak3.svg" alt="garis dekorasi" class="absolute -bottom-2 left-0 w-full h-auto -z-10">
                        </h2>
                        <p class="max-w-[550px] text-[14px] text-gray-500 leading-relaxed">
                            Berdedikasi untuk memberikan pelayanan kesehatan gigi berkualitas dengan dukungan teknologi medis terkini dan tim ahli yang kompeten.
                        </p>
                    </div>

                    <div class="flex flex-col gap-y-12 lg:gap-y-16 max-w-[900px] mx-auto">

                        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 text-center md:text-left">
                            <div class="w-[200px] h- lg:w-[240px] lg:h-[240px] rounded-[24px] overflow-hidden shadow-sm shrink-0">
                                <img src="assets/img/dokter-aris.png" alt="Drg. Aris Pratama, Sp.KG" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col justify-center pt-2">
                                <h3 class="text-[20px] font-extrabold text-slate-950 mb-2">
                                    Drg. Aris Pratama, Sp.KG <span class="text-[13px] font-medium text-gray-400 block md:inline md:ml-2">(Endodontik / Saraf Gigi)</span>
                                </h3>
                                <p class="text-[14px] text-gray-500 leading-[1.6]">
                                    drg. Aris menyediakan layanan perawatan saluran akar yang kompleks di DeCare Bali. Beliau memiliki pengalaman luas dalam menyelamatkan gigi asli dari infeksi saraf menggunakan teknologi mikroskopis terbaru. Aris lulusan Universitas Gadjah Mada dan dibesarkan di keluarga medis yang mengutamakan ketelitian.
                                </p>
                            </div>
                        </div>

                        <hr class="w-full border-t border-gray-200/60 my-2">

                        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 text-center md:text-left">
                            <div class="w-[200px] h-[50px] lg:w-[240px] lg:h-[240px] rounded-[24px] overflow-hidden shadow-sm shrink-0">
                                <img src="assets/img/dokter-rizky.png" alt="Drg. Rizky Wijaya" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col justify-center pt-2">
                                <h3 class="text-[20px] font-extrabold text-slate-950 mb-2">
                                    Drg. Rizky Wijaya <span class="text-[13px] font-medium text-gray-400 block md:inline md:ml-2">(Kedokteran Gigi Umum)</span>
                                </h3>
                                <p class="text-[14px] text-gray-500 leading-[1.6]">
                                    drg. Rizky fokus pada prosedur pemutihan gigi dan perawatan estetika ringan. Beliau sangat mendetail dalam menghilangkan noda gigi akibat kopi atau rokok, memastikan pasien pulang dengan senyum yang jauh lebih cerah. Rizky aktif dalam berbagai seminar kedokteran gigi modern di Asia.
                                </p>
                            </div>
                        </div>

                        <hr class="w-full border-t border-gray-200/60 my-2">

                        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 text-center md:text-left">
                            <div class="w-50 h-50 lg:w-[240px] lg:h-[240px] rounded-[24px] overflow-hidden shadow-sm shrink-0">
                                <img src="assets/img/dokter-budi.png" alt="Drg. Budi Santoso, Sp.BM" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col justify-center pt-2">
                                <h3 class="text-[20px] font-extrabold text-slate-950 mb-2">
                                    Drg. Budi Santoso, Sp.BM <span class="text-[13px] font-medium text-gray-400 block md:inline md:ml-2">(Bedah Mulut)</span>
                                </h3>
                                <p class="text-[14px] text-gray-500 leading-[1.6]">
                                    drg. Budi mengkhususkan diri dalam bedah mulut dan pemasangan implan gigi permanen. Memiliki pengalaman lebih dari 10 tahun, beliau dikenal sangat tenang dalam menangani kasus pencabutan gigi bungsu yang rumit. Budi merupakan lulusan terbaik dari Universitas Airlangga dengan fokus pada bedah rekonstruksi.
                                </p>
                            </div>
                        </div>

                        <hr class="w-full border-t border-gray-200/60 my-2">

                        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 text-center md:text-left">
                            <div class="w-50 h-50 lg:w-[240px] lg:h-[240px] rounded-[24px] overflow-hidden shadow-sm shrink-0">
                                <img src="assets/img/dokter-hendra.png" alt="Drg. Hendra Kusuma" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col justify-center pt-2">
                                <h3 class="text-[20px] font-extrabold text-slate-950 mb-2">
                                    Drg. Hendra Kusuma <span class="text-[13px] font-medium text-gray-400 block md:inline md:ml-2">(Kedokteran Gigi Umum)</span>
                                </h3>
                                <p class="text-[14px] text-gray-500 leading-[1.6]">
                                    drg. Hendra berfokus pada pendekatan preventif, mengedukasi pasien tentang pentingnya menjaga kebersihan mulut harian. Ramah dan sangat disukai oleh anak-anak, beliau ahli dalam melakukan scaling serta penambalan gigi struktural dengan minim rasa sakit.
                                </p>
                            </div>
                        </div>

                        <hr class="w-full border-t border-gray-200/60 my-2">

                        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 text-center md:text-left">
                            <div class="w-50 h-50 lg:w-[240px] lg:h-[240px] rounded-[24px] overflow-hidden shadow-sm shrink-0">
                                <img src="assets/img/dokter-farhan.png" alt="Drg. Farhan Aditya" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col justify-center pt-2">
                                <h3 class="text-[20px] font-extrabold text-slate-950 mb-2">
                                    Drg. Farhan Aditya <span class="text-[13px] font-medium text-gray-400 block md:inline md:ml-2">(Kedokteran Gigi Umum)</span>
                                </h3>
                                <p class="text-[14px] text-gray-500 leading-[1.6]">
                                    drg. Farhan menanani perawatan restoratif umum mulai dari perawatan gigi berlubang hingga pembuatan gigi tiruan lepasan. Dikenal dengan metodenya yang cekatan dan komunikatif, beliau selalu memastikan pasien memahami setiap langkah perawatan yang akan diambil secara transparan.
                                </p>
                            </div>
                        </div>

                        <hr class="w-full border-t border-gray-200/60 my-2">

                        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 text-center md:text-left">
                            <div class="w-50 h-50 lg:w-[240px] lg:h-[240px] rounded-[24px] overflow-hidden shadow-sm shrink-0">
                                <img src="assets/img/dokter-siti.png" alt="Drg. Siti Nurhaliza, Sp.Pros" class="w-full h-full object-cover">
                            </div>
                            <div class="flex flex-col justify-center pt-2">
                                <h3 class="text-[20px] font-extrabold text-slate-950 mb-2">
                                    Drg. Siti Nurhaliza, Sp.Pros <span class="text-[13px] font-medium text-gray-400 block md:inline md:ml-2">(Prostodonsia / Estetika)</span>
                                </h3>
                                <p class="text-[14px] text-gray-500 leading-[1.6]">
                                    drg. Siti adalah ahli estetika gigi yang fokus pada transformasi senyum melalui veneer dan mahkota gigi (crown). Beliau percaya bahwa setiap senyum memiliki karakter unik. Dengan latar belakang seni rekonstruksi yang kuat, Siti memastikan hasil prosedur kosmetik terlihat natural dan fungsional.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>

        <section id="tentang-kami" class="w-full py-12 lg:py-20 bg-white">

            <div class="max-w-[1170px] mx-auto px-[30px] lg:px-[60px] mb-20 lg:mb-28">

                <div class="mb-14 flex flex-col items-center text-center">
                    <h2 class="text-[36px] lg:text-[44px] font-black text-slate-900 mb-5 relative inline-block tracking-tight z-10">
                        Tentang Kami
                        <img src="assets/img/garis-abstrak2.svg" alt="garis dekorasi" class="absolute -bottom-2 left-0 w-full h-auto -z-10">
                    </h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">

                    <div class="lg:col-span-7 flex flex-col text-left">
                        <h3 class="text-[24px] lg:text-[28px] font-black text-slate-950 mb-4 tracking-tight">Misi Kami</h3>

                        <p class="text-[14px] text-gray-500 leading-[1.7] mb-6">
                            Di DeCare Bali, kenyamanan dan kesejahteraan pasien adalah prioritas utama kami. Kami berkomitmen membantu setiap pasien mencapai kesehatan mulut yang optimal melalui pendekatan yang menyeluruh dan personal. Kami tidak hanya fokus pada penanganan masalah gigi konvensional, tetapi juga memperhatikan aspek estetika, keseimbangan fungsional, dan kesehatan gusi jangka panjang untuk meningkatkan kualitas hidup Anda secara keseluruhan.
                        </p>

                        <h4 class="text-[16px] lg:text-[18px] font-extrabold text-slate-900 mb-4 leading-snug">
                            Dedikasi Kami Adalah Menciptakan Senyum Sehat, Alami, Dan Berkilau Bagi Setiap Pasien.
                        </h4>

                        <p class="text-[14px] text-gray-500 leading-[1.7]">
                            Klinik kami mengadopsi standar kedokteran gigi digital terbaru untuk memastikan setiap diagnosa dan rencana perawatan dilakukan secara presisi. Dengan dukungan peralatan mutakhir, tim spesialis kami mampu mensimulasikan hasil prosedur secara digital sebelum tindakan dilakukan, memastikan hasil yang optimal dan sesuai dengan keinginan pasien. Tim ahli di DeCare Bali merupakan pendukung kuat teknik minimal invasive dentistry, yang berarti prosedur dilakukan dengan trauma minimal, kenyamanan maksimal, dan waktu pemulihan yang jauh lebih cepat bagi Anda.
                        </p>
                    </div>

                    <div class="lg:col-span-5 w-full h-[360px] sm:h-[450px] lg:h-[500px] rounded-[32px] overflow-hidden shadow-md">
                        <img src="assets/img/gambar-tentangKami.png" alt="Prosedur Medis Presisi DeCare" class="w-full h-full object-cover">
                    </div>

                </div>
            </div>

            <div class="max-w-[1170px] mx-auto px-[30px] lg:px-[60px]">

                <div class="mb-12 flex flex-col items-center text-center">
                    <h3 class="text-[28px] lg:text-[36px] font-black text-slate-900 tracking-tight">
                        Senyum Bahagia Pasien DeCare
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

                    <div class="flex flex-col p-8 bg-white border border-gray-100 rounded-[24px] shadow-[0_10px_35px_rgba(0,0,0,0.03)] hover:shadow-md transition-shadow text-left h-full">
                        <div class="flex items-center gap-x-4 mb-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 shrink-0">
                                <img src="assets/img/pasien-kadek.png" alt="Kadek Dwipayana" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-[15px] font-bold text-slate-950 mb-0.5">Kadek Dwipayana</h4>
                                <img src="assets/img/icon-bintang.svg" alt="Rating 5 Bintang" class="h-3.5 w-auto object-contain">
                            </div>
                        </div>
                        <p class="text-[13px] text-gray-500 leading-[1.6] italic">
                            "Awalnya saya ragu melakukan perawatan salran akar karena saya dengar dengar sangat sakit. Tapi di DeCare, drg. Aris menjelaskannya dengan sangat detail lewat simulasi digital. Prosesnya tenang, hampir tidak terasa nyeri, dan gigi saya berhasil diselamatkan tanpa harus dicabut. Terima kasih!"
                        </p>
                    </div>

                    <div class="flex flex-col p-8 bg-white border border-gray-100 rounded-[24px] shadow-[0_10px_35px_rgba(0,0,0,0.03)] hover:shadow-md transition-shadow text-left h-full">
                        <div class="flex items-center gap-x-4 mb-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 shrink-0">
                                <img src="assets/img/pasien-luhputu.png" alt="Luh Putu Shanti" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-[15px] font-bold text-slate-950 mb-0.5">Luh Putu Shanti</h4>
                                <img src="assets/img/icon-bintang.svg" alt="Rating 5 Bintang" class="h-3.5 w-auto object-contain">
                            </div>
                        </div>
                        <p class="text-[13px] text-gray-500 leading-[1.6] italic">
                            "Senyum saya jadi jauh lebih percaya diri setelah melakukan prosedur restorasi di sini. drg. Siti benar-benar memperhatikan segala keluhan saya. Pelayanan administrasinya juga cepat dan kliniknnya sangat modern. Sukses terus DeCare!"
                        </p>
                    </div>

                    <div class="flex flex-col p-8 bg-white border border-gray-100 rounded-[24px] shadow-[0_10px_35px_rgba(0,0,0,0.03)] hover:shadow-md transition-shadow text-left h-full md:col-span-2 lg:col-span-1 max-w-[450px] md:max-w-none mx-auto lg:mx-0">
                        <div class="flex items-center gap-x-4 mb-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 shrink-0">
                                <img src="assets/img/pasien-putu.png" alt="Putu Aryawan" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-[15px] font-bold text-slate-950 mb-0.5">Putu Aryawan</h4>
                                <img src="assets/img/icon-bintang.svg" alt="Rating 5 Bintang" class="h-3.5 w-auto object-contain">
                            </div>
                        </div>
                        <p class="text-[13px] text-gray-500 leading-[1.6] italic">
                            "Awalnya takut ke dokter gigi, tapi tim di DeCare sangat ramah. Prosedur scaling-nya sama sekali tidak sakit dan hasilnya bersih sekali. reccomend banget!"
                        </p>
                    </div>

                </div>
            </div>

        </section>

        <footer class="w-full bg-white pt-16 pb-12 border-t border-gray-100">
            <div class="max-w-[1170px] mx-auto px-[30px] lg:px-[60px]">

                <div class="flex flex-col md:flex-row justify-between items-center gap-y-8 pb-10">
                    <div class="flex items-center">
                        <img src="assets/img/logo.svg" alt="DeCare Logo" class="h-9 w-auto">
                    </div>

                    <ul class="flex flex-wrap justify-center items-center gap-x-8 lg:gap-x-12 text-[15px] font-semibold text-slate-800">
                        <li><a href="#beranda" class="hover:text-blue-600 transition-colors">Beranda</a></li>
                        <li><a href="#layanan" class="hover:text-blue-600 transition-colors">Layanan</a></li>
                        <li><a href="#operasional" class="hover:text-blue-600 transition-colors">Operasional</a></li>
                        <li><a href="#tentang-kami" class="hover:text-blue-600 transition-colors">Tentang Kami</a></li>
                    </ul>
                </div>

                <hr class="w-full border-t-2 border-slate-900 mb-8">

                <div class="flex flex-col md:flex-row justify-between items-center gap-y-6">

                    <div class="text-[13px] text-slate-600 font-medium text-center md:text-left leading-relaxed">
                        All rights reserved &copy; <?= date('Y'); ?> DeCare.com <span class="hidden sm:inline mx-2">|</span> <br class="sm:hidden">
                        <a href="#" class="hover:underline hover:text-slate-900">Terms and conditions apply!</a>
                    </div>

                    <div class="flex items-center gap-x-4">
                        <a href="https://facebook.com" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-50 transition-colors active:scale-95">
                            <img src="assets/img/icon-fb.svg" alt="Facebook DeCare" class="w-6 h-6 object-contain">
                        </a>
                        <a href="https://instagram.com" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-50 transition-colors active:scale-95">
                            <img src="assets/img/icon-ig.svg" alt="Instagram DeCare" class="w-6 h-6 object-contain">
                        </a>
                        <a href="https://youtube.com" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-50 transition-colors active:scale-95">
                            <img src="assets/img/icon-yt.svg" alt="YouTube DeCare" class="w-6 h-6 object-contain">
                        </a>
                        <a href="https://linkedin.com" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-50 transition-colors active:scale-95">
                            <img src="assets/img/icon-in.svg" alt="LinkedIn DeCare" class="w-6 h-6 object-contain">
                        </a>
                        <a href="https://twitter.com" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-50 transition-colors active:scale-95">
                            <img src="assets/img/icon-tw.svg" alt="Twitter DeCare" class="w-6 h-6 object-contain">
                        </a>
                    </div>

                </div>

            </div>
        </footer>

        <?php
        // Mengulang hasil kueri layanan dari awal untuk dipakai di modal
        mysqli_data_seek($result_layanan, 0);
        while ($layanan = mysqli_fetch_assoc($result_layanan)):
            $id = $layanan['id_layanan'];

            // Tarik info statis modal dari array mapping, gunakan default jika tidak ketemu
            $icon = isset($info_layanan[$id]['icon']) ? $info_layanan[$id]['icon'] : 'icon-pencegahan.svg';
            $modal_desc = isset($info_layanan[$id]['modal_desc']) ? $info_layanan[$id]['modal_desc'] : 'Deskripsi perawatan lengkap dari DeCare.';
            $durasi = isset($info_layanan[$id]['durasi']) ? $info_layanan[$id]['durasi'] : 'Menyesuaikan';
        ?>
            <div id="modal-layanan-<?= $id ?>" class="hidden fixed inset-0 z-[100] bg-slate-950/70 backdrop-blur-sm items-center justify-center p-[30px] overflow-y-auto">
                <div class="relative w-full max-w-[680px] bg-white rounded-[32px] p-12 shadow-2xl text-center transform transition-all duration-300 scale-100 opacity-100 mt-10 mb-10">
                    <button onclick="closeModal(<?= $id ?>)" class="absolute top-8 right-8 text-slate-400 hover:text-red-500 transition-colors">
                        <img src="assets/img/icon-close.svg" alt="Tutup" class="w-6 h-6 object-contain">
                    </button>

                    <div class="flex items-center justify-center gap-x-6 mb-10 text-left border-b border-gray-100 pb-8">
                        <div class="flex items-center justify-center w-20 h-20 shrink-0 rounded-full bg-[#1da1f2] shadow-lg shadow-blue-200">
                            <img src="assets/img/<?= $icon ?>" alt="<?= htmlspecialchars($layanan['nama_layanan']) ?>" class="w-10 h-10 object-contain">
                        </div>
                        <h3 class="text-[28px] lg:text-[32px] font-black text-slate-950 leading-tight">
                            Layanan <br> <?= htmlspecialchars($layanan['nama_layanan']) ?>
                        </h3>
                    </div>

                    <p class="text-[14px] lg:text-[15px] text-gray-600 leading-relaxed mb-10 max-w-[550px] mx-auto text-center">
                        <?= $modal_desc ?>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-blue-50/50 rounded-[20px] p-8 text-left border border-blue-100/50">
                        <div class="border-b md:border-b-0 md:border-r border-blue-100/70 pb-6 md:pb-0 md:pr-8 flex items-start gap-x-4">
                            <div class="flex items-center justify-center w-10 h-10 bg-white border border-blue-50 rounded-full shadow-md shrink-0">
                                <img src="assets/img/icon-price.svg" alt="Estimasi waktu" class="w-6 h-6 object-contain">
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Estimasi Harga (Mulai Dari)</p>
                                <p class="text-[16px] font-bold text-blue-600">Rp <?= number_format($layanan['harga'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-x-4">
                            <div class="flex items-center justify-center w-10 h-10 bg-white border border-blue-50 rounded-full shadow-md shrink-0">
                                <img src="assets/img/icon-timer.svg" alt="Estimasi waktu" class="w-6 h-6 object-contain">
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Durasi Rata-Rata</p>
                                <p class="text-[16px] font-bold text-slate-950 whitespace-nowrap"><?= $durasi ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

    </main>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const menuClose = document.getElementById('menu-close');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileOverlay = document.getElementById('mobile-overlay');

        // Fungsi Buka Menu
        menuToggle.addEventListener('click', () => {
            // Tampilkan Overlay Gelap
            mobileOverlay.classList.remove('hidden');
            mobileOverlay.classList.add('block');
            setTimeout(() => mobileOverlay.classList.add('opacity-100'), 10);

            // Tampilkan Laci Menu
            mobileMenu.classList.remove('hidden');
            mobileMenu.classList.add('flex');
            setTimeout(() => mobileMenu.classList.remove('translate-x-full'), 10);

            // Kunci layar belakang agar tidak bisa di-scroll
            document.body.style.overflow = 'hidden';
        });

        // Fungsi Tutup Menu
        const closeMobileMenu = () => {
            // Sembunyikan Overlay Gelap
            mobileOverlay.classList.remove('opacity-100');
            setTimeout(() => {
                mobileOverlay.classList.add('hidden');
                mobileOverlay.classList.remove('block');
            }, 300); // Menunggu animasi selesai (300ms)

            // Sembunyikan Laci Menu
            mobileMenu.classList.add('translate-x-full');
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
            }, 300);

            // Buka kembali kunci scroll layar
            document.body.style.overflow = 'auto';
        };

        menuClose.addEventListener('click', closeMobileMenu);
        mobileOverlay.addEventListener('click', closeMobileMenu);


        // ==========================================
        // FITUR SCROLLSPY (Deteksi Bagian Aktif)
        // ==========================================

        const sections = document.querySelectorAll("section[id]");

        // Memisahkan link Desktop dan Mobile
        const desktopLinks = document.querySelectorAll("nav ul.hidden li a");
        const mobileLinks = document.querySelectorAll("#mobile-menu ul li a");

        // Fitur tambahan: Otomatis tutup menu mobile saat link diklik
        mobileLinks.forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });

        window.addEventListener("scroll", () => {
            let current = "";

            sections.forEach((section) => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute("id");
                }
            });

            // 1. ganti warna dektopnya
            desktopLinks.forEach((link) => {
                link.classList.remove("text-gray-950", "font-semibold");
                link.classList.add("text-gray-500", "font-medium");

                if (link.getAttribute("href") === "#" + current) {
                    link.classList.add("text-gray-950", "font-semibold");
                    link.classList.remove("text-gray-500", "font-medium");
                }
            });

            // 2. Atur menu mobile
            mobileLinks.forEach((link) => {
                // Kembalikan ke mode polos
                link.classList.remove("border-blue-600", "text-blue-600", "bg-blue-50/50");
                link.classList.add("border-transparent", "text-slate-700");

                // Kalau aktif, kasih warna biru
                if (link.getAttribute("href") === "#" + current) {
                    link.classList.remove("border-transparent", "text-slate-700");
                    link.classList.add("border-blue-600", "text-blue-600", "bg-blue-50/50");
                }
            });
        });

        // ==========================================
        // FITUR MODAL
        // ==========================================

        function openModal(id) {
            const modal = document.getElementById('modal-layanan-' + id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const modal = document.getElementById('modal-layanan-' + id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
        }
    </script>
</body>

</html>
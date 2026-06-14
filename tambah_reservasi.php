<?php
require_once 'functions.php';
/** @var mysqli $koneksi */
wajibLogin();

$errors = [];

// 1. Ambil data layanan aktif dari database
$queryLayanan = "SELECT * FROM layanan";
$resLayanan   = mysqli_query($koneksi, $queryLayanan);

// 2. Ambil data jadwal dokter beserta nama dokternya
$queryJadwal = "SELECT jadwal.*, dokter.nama_dokter, dokter.id_layanan 
                FROM jadwal 
                INNER JOIN dokter ON jadwal.id_dokter = dokter.id_dokter";
$resJadwal   = mysqli_query($koneksi, $queryJadwal);

// 3. Khusus Pasien Login: Ambil semua daftar keluarga yang terikat dengan id_user ini
$list_keluarga = [];
if ($_SESSION['role'] === 'pasien') {
    $id_user_login = (int)$_SESSION['user_id'];
    $q_keluarga = mysqli_query($koneksi, "SELECT * FROM pasien WHERE id_user = $id_user_login");
    while ($row = mysqli_fetch_assoc($q_keluarga)) {
        $list_keluarga[] = $row;
    }
}

if (isset($_POST['simpan'])) {
    $keluhan           = htmlspecialchars(trim($_POST['keluhan'] ?? ''));

    //kasih nilai default 0 jika array key tidak ditemukan/kosog
    $id_layanan        = (int)($_POST['id_layanan'] ?? 0);
    $id_jadwal         = (int)($_POST['id_jadwal'] ?? 0);

    $tanggal_reservasi = $_POST['tanggal_reservasi'] ?? '';
    $id_pasien         = null;

    // Validasi Dasar Pilihan Medis
    if (empty($keluhan)) {
        $errors[] = "Keluhan utama pasien wajib diisi!";
    }
    if (empty($id_layanan)) {
        $errors[] = "Silakan pilih jenis layanan perawatan!";
    }
    if (empty($id_jadwal)) {
        $errors[] = "Silakan pilih dokter dan jam praktek!";
    }

    // Validasi Tanggal Operasional
    if (empty($tanggal_reservasi)) {
        $errors[] = "Tanggal kunjungan wajib ditentukan!";
    } else {
        $hari_pilihan = date('D', strtotime($tanggal_reservasi));
        if ($hari_pilihan === 'Sun') {
            $errors[] = "Klinik DeCare tutup pada hari Minggu! Silakan pilih hari operasional Senin - Sabtu.";
        } else {
            // Validasi kesesuaian hari praktek dokter di sisi server
            $qJadwalDetail = mysqli_query($koneksi, "SELECT hari FROM jadwal WHERE id_jadwal = $id_jadwal LIMIT 1");
            if ($jd = mysqli_fetch_assoc($qJadwalDetail)) {
                $teks_hari = strtolower($jd['hari']);
                $is_valid_day = false;
                $map_hari = ['Mon' => 'senin', 'Tue' => 'selasa', 'Wed' => 'rabu', 'Thu' => 'kamis', 'Fri' => 'jumat', 'Sat' => 'sabtu'];
                $keyword_hari = $map_hari[$hari_pilihan];

                if (strpos($teks_hari, $keyword_hari) !== false) {
                    $is_valid_day = true;
                } elseif (strpos($teks_hari, 'sampai') !== false || strpos($teks_hari, 's/d') !== false) {
                    if ($hari_pilihan !== 'Sat') {
                        $is_valid_day = true;
                    }
                }
                if (!$is_valid_day) {
                    $errors[] = "Tanggal kunjungan tidak cocok dengan hari praktek dokter!";
                }
            }
        }
    }

    // Pengecekan Tabrakan Jadwal Slot Dokter
    if (empty($errors)) {
        $queryCekBentrok = "SELECT id_reservasi FROM reservasi WHERE id_jadwal = ? AND tanggal_reservasi = ? AND status_periksa != 'Dibatalkan'";
        $stmtCek = mysqli_prepare($koneksi, $queryCekBentrok);
        mysqli_stmt_bind_param($stmtCek, "is", $id_jadwal, $tanggal_reservasi);
        mysqli_stmt_execute($stmtCek);
        if (mysqli_num_rows(mysqli_stmt_get_result($stmtCek)) > 0) {
            $errors[] = "Maaf, slot jadwal dokter pada tanggal tersebut sudah penuh dipesan pasien lain!";
        }
        mysqli_stmt_close($stmtCek);
    }

    // Penanganan Unggah Gambar Berkas
    $namaFileBaru = NULL;
    if (isset($_FILES['foto_gigi']) && $_FILES['foto_gigi']['error'] === 0) {
        $fileSize = $_FILES['foto_gigi']['size'];
        $fileTmp  = $_FILES['foto_gigi']['tmp_name'];
        $fileName = $_FILES['foto_gigi']['name'];
        $ekstensiValid = ['jpg', 'jpeg', 'png'];
        $ekstensiFile  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ekstensiFile, $ekstensiValid)) {
            $errors[] = "Format berkas salah! Harus JPG, JPEG, atau PNG.";
        }
        if ($fileSize > 2097152) {
            $errors[] = "Ukuran file gambar terlalu besar! Maksimal 2MB.";
        }

        if (empty($errors)) {
            $namaFileBaru = uniqid() . '.' . $ekstensiFile;
            if (!file_exists('uploads/')) {
                mkdir('uploads/', 0775, true);
            }
            move_uploaded_file($fileTmp, 'uploads/' . $namaFileBaru);
        }
    }

    // PROSES PENENTUAN ID PASIEN SECARA MULTI-PROFIL
    if (empty($errors)) {
        if ($_SESSION['role'] === 'pasien') {
            $pilihan_pasien = $_POST['pilihan_pasien'];

            if ($pilihan_pasien === 'baru') {
                // Alur input profil anggota keluarga baru atau pasien
                $nama_baru = htmlspecialchars(trim($_POST['nama_pasien_baru']));
                $hp_baru   = htmlspecialchars(trim($_POST['nomor_hp_baru']));
                $alamat_baru = htmlspecialchars(trim($_POST['alamat_baru']));

                if (empty($nama_baru)) {
                    $errors[] = "Nama anggota keluarga/pasien baru wajib diisi!";
                }
                if (empty($hp_baru)) {
                    $errors[] = "Nomor HP anggota keluarga/pasien baru wajib diisi!";
                }

                if (empty($errors)) {
                    // Masukkan ke tabel pasien dengan id_user induk yang sama
                    $id_user_login = (int)$_SESSION['user_id'];
                    $q_ins_pasien = "INSERT INTO pasien (id_user, nama_lengkap, nomor_hp, alamat) VALUES (?, ?, ?, ?)";
                    $stmt_p = mysqli_prepare($koneksi, $q_ins_pasien);
                    mysqli_stmt_bind_param($stmt_p, "isss", $id_user_login, $nama_baru, $hp_baru, $alamat_baru);

                    if (mysqli_stmt_execute($stmt_p)) {
                        $id_pasien = mysqli_insert_id($koneksi);
                    }
                    mysqli_stmt_close($stmt_p);
                }
            } else {
                // Menggunakan profil keluarga/diri sendiri yang dipilih dari dropdown
                $id_pasien = (int)$pilihan_pasien;
            }
        } else {
            // ========================================================
            // ALUR SISI ADMIN: OTOMATIS DAFTAR JIKA BELUM ADA DI SISTEM
            // ========================================================
            $nama_admin = htmlspecialchars(trim($_POST['nama_pasien_admin']));
            $hp_admin   = htmlspecialchars(trim($_POST['nomor_hp_admin']));

            if (empty($nama_admin)) {
                $errors[] = "Nama lengkap pasien wajib diisi oleh admin!";
            }

            if (empty($errors)) {
                // 1. Cek dulu apakah pasien dengan nama atau HP tersebut sudah pernah terdaftar
                $q_cari = mysqli_query($koneksi, "SELECT id_pasien FROM pasien WHERE nama_lengkap = '$nama_admin' OR nomor_hp = '$hp_admin' LIMIT 1");

                if ($row_p = mysqli_fetch_assoc($q_cari)) {
                    // Jika sudah terdaftar, langsung ambil ID Pasien lamanya
                    $id_pasien = $row_p['id_pasien'];
                } else {
                    // Jika BELUM terdaftar, otomatis buatkan profil baru di bawah id_user Admin (id_user = 1)
                    $id_admin_induk = 1; // ID User Admin utama di database kamu
                    $alamat_default  = "Didaftarkan Offline oleh Admin";

                    $q_auto_pasien = "INSERT INTO pasien (id_user, nama_lengkap, nomor_hp, alamat) VALUES (?, ?, ?, ?)";
                    $stmt_auto = mysqli_prepare($koneksi, $q_auto_pasien);
                    mysqli_stmt_bind_param($stmt_auto, "isss", $id_admin_induk, $nama_admin, $hp_admin, $alamat_default);

                    if (mysqli_stmt_execute($stmt_auto)) {
                        // Ambil ID Pasien baru yang barusan tercipta otomatis
                        $id_pasien = mysqli_insert_id($koneksi);
                    }
                    mysqli_stmt_close($stmt_auto);
                }
            }
        }

        // JIKA ID PASIEN SUDAH DIKUNCI, SIMPAN ANTRIAN RESERVASI
        if (empty($errors) && $id_pasien !== null) {
            $query = "INSERT INTO reservasi (id_pasien, id_layanan, id_jadwal, tanggal_reservasi, keluhan, foto_gigi) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt  = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "iiisss", $id_pasien, $id_layanan, $id_jadwal, $tanggal_reservasi, $keluhan, $namaFileBaru);

            if (mysqli_stmt_execute($stmt)) {
                if ($_SESSION['role'] === 'admin') {
                    echo "<script>alert('Reservasi pasien berhasil disimpan!'); window.location='dashboard_reservasi.php';</script>";
                } else {
                    echo "<script>alert('Reservasi keluarga berhasil dibuat! Mohon tunggu konfirmasi Admin.'); window.location='index.php';</script>";
                }
                exit;
            } else {
                $errors[] = "Gagal memproses simpan antrean ke server.";
            }
        }
    }
}

$link_kembali = ($_SESSION['role'] === 'admin') ? 'dashboard_reservasi.php' : 'index.php';
$teks_kembali = ($_SESSION['role'] === 'admin') ? 'Kembali ke Dashboard' : 'Kembali ke Beranda';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Reservasi Pasien - DeCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-50/20 font-sans min-h-screen p-4 sm:p-8 flex items-center justify-center">
    <div class="max-w-[600px] w-full bg-white p-6 sm:p-8 rounded-[32px] border border-blue-100/40 shadow-xl">
        <div class="mb-6 border-b border-gray-100 pb-4">
            <a href="<?= $link_kembali ?>" class="text-xs font-bold text-blue-600 uppercase hover:underline flex items-center gap-x-1">← <?= $teks_kembali ?></a>
            <h1 class="text-2xl font-black text-slate-900 mt-2">Buat Janji Temu Medis</h1>
            <p class="text-gray-400 text-xs mt-0.5">Satu akun untuk pengelolaan reservasi medis seluruh anggota keluarga.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 text-red-600 border border-red-100 p-4 rounded-xl mb-6 text-xs font-medium flex flex-col gap-y-1">
                <?php foreach ($errors as $err): ?><span>• <?= $err ?></span><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col gap-y-4">

            <?php if ($_SESSION['role'] === 'pasien'): ?>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Anggota Keluarga yang Diperiksa</label>
                    <select name="pilihan_pasien" id="pilihan_pasien" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:border-blue-500">
                        <?php foreach ($list_keluarga as $fam): ?>
                            <option value="<?= $fam['id_pasien'] ?>"><?= htmlspecialchars($fam['nama_lengkap']) ?> (Profil Terdaftar)</option>
                        <?php endforeach; ?>
                        <option value="baru" class="text-blue-600 font-bold">+ Tambah anggota keluarga/pasien baru</option>
                    </select>
                </div>

                <div id="form_pasien_baru" class="hidden p-4 bg-slate-50 border border-slate-100 rounded-2xl flex flex-col gap-y-3">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Biodata Anggota Keluarga Baru</span>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Nama Lengkap Sesuai Kartu Keluarga</label>
                        <input type="text" name="nama_pasien_baru" placeholder="Contoh: Nama Anak Anda" class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Nomor HP / WhatsApp Aktif</label>
                        <input type="text" name="nomor_hp_baru" placeholder="Contoh: 08xxxxxxxx" class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Alamat Domisili Rumah</label>
                        <input type="text" name="alamat_baru" placeholder="Alamat tinggal" class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none">
                    </div>
                </div>

            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap Pasien</label>
                        <input type="text" name="nama_pasien_admin" placeholder="Ketik nama pasien..." class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor HP Pasien</label>
                        <input type="text" name="nomor_hp_admin" placeholder="Ketik nomor kontak..." class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm">
                    </div>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jenis Perawatan Layanan</label>
                <select name="id_layanan" id="id_layanan" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>-- Pilih Jenis Layanan --</option>
                    <?php mysqli_data_seek($resLayanan, 0);
                    while ($layanan = mysqli_fetch_assoc($resLayanan)): ?>
                        <option value="<?= $layanan['id_layanan'] ?>" <?= (isset($_POST['id_layanan']) && $_POST['id_layanan'] == $layanan['id_layanan']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($layanan['nama_layanan']) ?> (Rp <?= number_format($layanan['harga'], 0, ',', '.') ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Dokter Spesialis & Jam Kerja</label>
                <select name="id_jadwal" id="id_jadwal" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm bg-white focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>-- Pilih Dokter & Jam Kerja --</option>
                    <?php mysqli_data_seek($resJadwal, 0);
                    while ($jadwal = mysqli_fetch_assoc($resJadwal)): ?>
                        <option value="<?= $jadwal['id_jadwal'] ?>" data-layanan="<?= $jadwal['id_layanan'] ?>" data-hariteks="<?= htmlspecialchars(strtolower($jadwal['hari'])) ?>" <?= (isset($_POST['id_jadwal']) && $_POST['id_jadwal'] == $jadwal['id_jadwal']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($jadwal['nama_dokter']) ?> [<?= $jadwal['hari'] ?>]
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Tanggal Kunjungan</label>
                <input type="date" name="tanggal_reservasi" id="tanggal_reservasi" min="<?= date('Y-m-d') ?>" value="<?= isset($_POST['tanggal_reservasi']) ? htmlspecialchars($_POST['tanggal_reservasi']) : '' ?>" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-blue-500">
                <p id="error-hari-js" class="text-red-500 text-[11px] mt-1 hidden font-semibold">⚠️ Tanggal tidak cocok dengan jadwal operasional dokter pilihan!</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keluhan Utama Gigi</label>
                <textarea name="keluhan" rows="3" placeholder="Tuliskan keluhan atau gejala sakit gigi..." class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm leading-relaxed"><?= isset($_POST['keluhan']) ? htmlspecialchars($_POST['keluhan']) : '' ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Upload Foto Kondisi Gigi (Opsional)</label>
                <input type="file" name="foto_gigi" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition-all cursor-pointer">
            </div>

            <button type="submit" name="simpan" class="bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition-all mt-2 text-sm shadow-md shadow-blue-100">Kirim Data Reservasi</button>
        </form>
    </div>

    <script>
        const selectLayanan = document.getElementById('id_layanan');
        const selectJadwal = document.getElementById('id_jadwal');
        const inputTanggal = document.getElementById('tanggal_reservasi');
        const errorHariJs = document.getElementById('error-hari-js');
        const opsiDokter = selectJadwal.querySelectorAll('option');

        // Elemen khusus alur multi-pasien keluarga
        const selectPasien = document.getElementById('pilihan_pasien');
        const formPasienBaru = document.getElementById('form_pasien_baru');

        // Toggle Form Pasien Baru khusus untuk role Pasien
        if (selectPasien) {
            selectPasien.addEventListener('change', function() {
                if (this.value === 'baru') {
                    formPasienBaru.classList.remove('hidden');
                } else {
                    formPasienBaru.classList.add('hidden');
                }
            });
        }

        // Saring Dokter berdasarkan Layanan
        selectLayanan.addEventListener('change', function() {
            const idLayananTerpilih = this.value;
            selectJadwal.value = "";
            inputTanggal.value = "";
            errorHariJs.classList.add('hidden');

            opsiDokter.forEach(option => {
                if (option.value === "") {
                    option.style.display = "block";
                    return;
                }
                const keahlianDokter = option.getAttribute('data-layanan');
                if (keahlianDokter === idLayananTerpilih) {
                    option.style.display = "block";
                } else {
                    option.style.display = "none";
                }
            });
        });

        // Validasi Tanggal Kunjungan vs Hari Dokter
        inputTanggal.addEventListener('change', function() {
            const tanggalUser = new Date(this.value);
            if (isNaN(tanggalUser)) return;

            const hariInggris = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
            const namaHariUser = hariInggris[tanggalUser.getDay()];
            const kamusHari = {
                'mon': 'senin',
                'tue': 'selasa',
                'wed': 'rabu',
                'thu': 'kamis',
                'fri': 'jumat',
                'sat': 'sabtu'
            };

            if (namaHariUser === 'sun') {
                alert('Hari Minggu Klinik Tutup! Silakan pilih hari lain.');
                this.value = "";
                return;
            }

            const dokterTerpilih = selectJadwal.options[selectJadwal.selectedIndex];
            if (!dokterTerpilih || dokterTerpilih.value === "") return;

            const hariTeksDokter = dokterTerpilih.getAttribute('data-hariteks');
            const kataKunciHari = kamusHari[namaHariUser];

            let isValid = false;
            if (hariTeksDokter.includes(kataKunciHari)) {
                isValid = true;
            } else if (hariTeksDokter.includes('sampai') || hariTeksDokter.includes('s/d')) {
                if (namaHariUser !== 'sat') {
                    isValid = true;
                }
            }

            if (!isValid) {
                errorHariJs.classList.remove('hidden');
                this.value = "";
            } else {
                errorHariJs.classList.add('hidden');
            }
        });

        selectJadwal.addEventListener('change', function() {
            inputTanggal.value = "";
            errorHariJs.classList.add('hidden');
        });
    </script>
</body>

</html>
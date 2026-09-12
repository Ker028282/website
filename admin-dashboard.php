<?php
session_start();
require 'koneksi.php';

// Set zona waktu Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// Cek Keamanan Login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}

// Proses Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: admin-login.php");
    exit;
}

$alert_message = '';

// --- LOGIC TAMBAH PESANAN ---
if (isset($_POST['tambah_order'])) {
    $no_resi   = mysqli_real_escape_string($conn, $_POST['no_resi']);
    $nama_klien= mysqli_real_escape_string($conn, $_POST['nama_klien']);
    $no_wa     = mysqli_real_escape_string($conn, $_POST['no_wa']);
    $layanan   = mysqli_real_escape_string($conn, $_POST['layanan']);
    $harga     = intval($_POST['harga']);
    $status    = mysqli_real_escape_string($conn, $_POST['status']);

    $query_insert = "INSERT INTO pesanan (no_resi, nama_klien, layanan, harga, status) VALUES ('$no_resi', '$nama_klien ($no_wa)', '$layanan', '$harga', '$status')";
    if (mysqli_query($conn, $query_insert)) {
        header("Location: admin-dashboard.php?status=sukses_tambah");
        exit;
    }
}

// --- LOGIC EDIT PESANAN ---
if (isset($_POST['edit_order'])) {
    $id        = intval($_POST['id']);
    $no_resi   = mysqli_real_escape_string($conn, $_POST['no_resi']);
    $nama_klien= mysqli_real_escape_string($conn, $_POST['nama_klien']);
    $layanan   = mysqli_real_escape_string($conn, $_POST['layanan']);
    $harga     = intval($_POST['harga']);
    $status    = mysqli_real_escape_string($conn, $_POST['status']);

    $query_update = "UPDATE pesanan SET no_resi='$no_resi', nama_klien='$nama_klien', layanan='$layanan', harga='$harga', status='$status' WHERE id=$id";
    if (mysqli_query($conn, $query_update)) {
        header("Location: admin-dashboard.php?status=sukses_edit");
        exit;
    }
}

// --- LOGIC HAPUS PESANAN ---
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $query_delete = "DELETE FROM pesanan WHERE id = $id_hapus";
    if (mysqli_query($conn, $query_delete)) {
        header("Location: admin-dashboard.php?status=sukses_hapus");
        exit;
    }
}

// Ambil data untuk mode edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $q_edit = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $id_edit");
    $edit_data = mysqli_fetch_assoc($q_edit);
}

// Tangkap notifikasi Flash dari URL parameter
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'sukses_tambah') {
        $alert_message = "Pesanan baru berhasil ditambahkan ke sistem!";
    } elseif ($_GET['status'] == 'sukses_edit') {
        $alert_message = "Data pesanan berhasil diperbarui!";
    } elseif ($_GET['status'] == 'sukses_hapus') {
        $alert_message = "Pesanan berhasil dihapus dari database!";
    }
}

// --- STATISTIK DASHBOARD ---
$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan"))['total'];
$total_proses  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Dikerjakan'"))['total'];
$total_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'Selesai'"))['total'];

$q_uang = mysqli_query($conn, "SELECT SUM(harga) as cuan FROM pesanan WHERE status = 'Selesai'");
$total_pendapatan = mysqli_fetch_assoc($q_uang)['cuan'] ?? 0;
$cuan_rupiah = "Rp " . number_format($total_pendapatan, 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FARoki.xyz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased relative">

    <!-- Flash Notification Banner -->
    <?php if (!empty($alert_message)): ?>
    <div id="flashAlert" class="fixed top-5 right-5 z-50 bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 border border-emerald-500 animate-bounce">
        <i class="fa-solid fa-circle-check text-xl"></i>
        <span class="text-sm font-semibold"><?php echo $alert_message; ?></span>
    </div>
    <script>
        setTimeout(() => {
            const alertBox = document.getElementById('flashAlert');
            if(alertBox) {
                alertBox.style.transition = 'opacity 0.5s ease';
                alertBox.style.opacity = '0';
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 4000);
    </script>
    <?php endif; ?>

    <!-- Top Navbar -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <i class="fa-solid fa-user-shield text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">FARoki Admin</span>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-400 hidden sm:block">Halo, Bos Faroki!</span>
                <a href="?action=logout" class="bg-red-500/10 hover:bg-red-500 hover:text-white text-red-500 transition-colors px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 border border-red-500/20">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header & Waktu Real-Time WIB -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">Overview Dashboard</h1>
                <p class="text-slate-400 text-sm">Kelola pesanan klien dengan sistem profesional.</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 px-5 py-3 rounded-xl flex items-center gap-3 shadow-md">
                <div class="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center">
                    <i class="fa-regular fa-clock text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium">Waktu Server (WIB)</p>
                    <p class="text-sm font-bold text-white"><?php echo date('l, d F Y - H:i'); ?> WIB</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl">
                <p class="text-slate-400 text-sm font-medium mb-1">Total Pesanan</p>
                <h3 class="text-3xl font-bold text-white"><?php echo $total_pesanan; ?></h3>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl">
                <p class="text-slate-400 text-sm font-medium mb-1">Sedang Dikerjakan</p>
                <h3 class="text-3xl font-bold text-amber-400"><?php echo $total_proses; ?></h3>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl">
                <p class="text-slate-400 text-sm font-medium mb-1">Pesanan Selesai</p>
                <h3 class="text-3xl font-bold text-emerald-400"><?php echo $total_selesai; ?></h3>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl">
                <p class="text-slate-400 text-sm font-medium mb-1">Total Pendapatan (Selesai)</p>
                <h3 class="text-2xl font-bold text-blue-400"><?php echo $cuan_rupiah; ?></h3>
            </div>
        </div>

        <!-- Tabel Orderan dengan Search & Filter -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                <h2 class="text-lg font-bold">Daftar Orderan Terbaru</h2>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative flex-grow sm:w-64">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" id="searchInput" onkeyup="filterTabel()" placeholder="Cari nama atau resi..." class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-9 pr-4 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <button onclick="openModal('modalTambah')" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl transition-colors font-semibold flex items-center gap-2 shadow-lg shadow-blue-600/20 shrink-0">
                        <i class="fa-solid fa-plus"></i> Tambah Order
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table id="tabelPesanan" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900/50 text-slate-400 text-sm">
                            <th class="p-4 font-medium">No. Resi</th>
                            <th class="p-4 font-medium">Nama Klien</th>
                            <th class="p-4 font-medium">Layanan</th>
                            <th class="p-4 font-medium">Harga</th>
                            <th class="p-4 font-medium">Status</th>
                            <th class="p-4 font-medium text-right">Aksi & Quick Chat</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-800/50">
                        <?php
                        $query_pesanan = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY id DESC");
                        while($row = mysqli_fetch_assoc($query_pesanan)) {
                            if($row['status'] == 'Pending') {
                                $badge = '<span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-3 py-1 rounded-full text-xs font-semibold">Pending</span>';
                            } elseif($row['status'] == 'Dikerjakan') {
                                $badge = '<span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1.5 w-fit"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Dikerjakan</span>';
                            } else {
                                $badge = '<span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded-full text-xs font-semibold">Selesai</span>';
                            }
                        ?>
                        <tr class="hover:bg-slate-800/20 transition-colors item-row">
                            <td class="p-4 font-mono text-blue-400 font-bold"><?php echo $row['no_resi']; ?></td>
                            <td class="p-4 font-semibold text-slate-200"><?php echo $row['nama_klien']; ?></td>
                            <td class="p-4 text-slate-400"><?php echo $row['layanan']; ?></td>
                            <td class="p-4 text-slate-300">Rp <?php echo number_format($row['harga'],0,',','.'); ?></td>
                            <td class="p-4"><?php echo $badge; ?></td>
                            <td class="p-4 text-right flex items-center justify-end gap-2">
                                <!-- Tombol Quick WhatsApp -->
                                <a href="https://wa.me/?text=Halo%20<?php echo urlencode($row['nama_klien']); ?>,%20terkait%20pesanan%20resimu%20(<?php echo $row['no_resi']; ?>)..." target="_blank" class="bg-emerald-600/20 hover:bg-emerald-600 hover:text-white text-emerald-400 transition-colors p-2 rounded-lg text-xs" title="Chat Klien via WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                                <!-- Tombol Edit -->
                                <a href="?edit=<?php echo $row['id']; ?>" class="bg-slate-800 hover:bg-blue-600 hover:text-white text-slate-300 transition-colors px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-1">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <!-- Tombol Hapus (Memicu Modal Konfirmasi) -->
                                <button onclick="konfirmasiHapus(<?php echo $row['id']; ?>, '<?php echo $row['no_resi']; ?>')" class="bg-red-500/10 hover:bg-red-600 hover:text-white text-red-400 transition-colors px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-1">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL KONFIRMASI HAPUS -->
    <div id="modalHapus" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-sm rounded-3xl p-6 text-center shadow-2xl">
            <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Hapus Pesanan?</h3>
            <p class="text-slate-400 text-xs mb-6">Data resi <span id="textResi" class="text-blue-400 font-mono font-bold"></span> akan dihapus permanen dari database.</p>
            <div class="flex gap-3">
                <button onclick="closeModal('modalHapus')" class="w-1/2 bg-slate-800 hover:bg-slate-700 text-slate-300 py-2.5 rounded-xl font-semibold text-xs transition-colors">Batal</button>
                <a id="btnHapusFinal" href="#" class="w-1/2 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl font-semibold text-xs transition-colors flex items-center justify-center">Ya, Hapus</a>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH ORDER -->
    <div id="modalTambah" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl p-6 sm:p-8 shadow-2xl relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Tambah Pesanan Baru</h3>
                <button onclick="closeModal('modalTambah')" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">No. Resi Unik</label>
                    <input type="text" name="no_resi" required value="FRK-<?php echo rand(100,999); ?>" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nama Klien</label>
                    <input type="text" name="nama_klien" required placeholder="Contoh: Budi Santoso" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">No. WhatsApp Klien</label>
                    <input type="text" name="no_wa" placeholder="Contoh: 628123456789" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Pilih Layanan</label>
                    <select name="layanan" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500">
                        <option value="Jasa Ketik & Rapiin Makalah">Jasa Ketik & Rapiin Makalah</option>
                        <option value="Desain PPT Estetik">Desain PPT Estetik</option>
                        <option value="Jasa Parafrase (Anti-Turnitin)">Jasa Parafrase (Anti-Turnitin)</option>
                        <option value="Translate & Resume Jurnal">Translate & Resume Jurnal</option>
                        <option value="Transkrip Wawancara">Transkrip Wawancara</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Harga (Angka Saja)</label>
                    <input type="number" name="harga" required placeholder="Contoh: 25000" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Status Pengerjaan</label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Dikerjakan">Dikerjakan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal('modalTambah')" class="w-1/2 bg-slate-800 hover:bg-slate-700 text-slate-300 py-3 rounded-xl font-semibold text-sm transition-colors">Batal</button>
                    <button type="submit" name="tambah_order" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold text-sm transition-colors shadow-lg shadow-blue-600/30">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PESANAN -->
    <?php if ($edit_data): ?>
    <div id="modalEdit" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl p-6 sm:p-8 shadow-2xl relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Edit Pesanan: <span class="text-blue-400"><?php echo $edit_data['no_resi']; ?></span></h3>
                <a href="admin-dashboard.php" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></a>
            </div>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">No. Resi</label>
                    <input type="text" name="no_resi" required value="<?php echo $edit_data['no_resi']; ?>" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nama Klien</label>
                    <input type="text" name="nama_klien" required value="<?php echo $edit_data['nama_klien']; ?>" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Pilih Layanan</label>
                    <input type="text" name="layanan" required value="<?php echo $edit_data['layanan']; ?>" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" required value="<?php echo $edit_data['harga']; ?>" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Status Pengerjaan</label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-blue-500">
                        <option value="Pending" <?php if($edit_data['status']=='Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Dikerjakan" <?php if($edit_data['status']=='Dikerjakan') echo 'selected'; ?>>Dikerjakan</option>
                        <option value="Selesai" <?php if($edit_data['status']=='Selesai') echo 'selected'; ?>>Selesai</option>
                    </select>
                </div>
                <div class="pt-4 flex gap-3">
                    <a href="admin-dashboard.php" class="w-1/2 text-center bg-slate-800 hover:bg-slate-700 text-slate-300 py-3 rounded-xl font-semibold text-sm transition-colors">Batal</a>
                    <button type="submit" name="edit_order" class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold text-sm transition-colors shadow-lg shadow-blue-600/30">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- JavaScript Interaktif -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Fungsi Modal Konfirmasi Hapus
        function konfirmasiHapus(id, resi) {
            document.getElementById('textResi').innerText = resi;
            document.getElementById('btnHapusFinal').href = "?hapus=" + id;
            openModal('modalHapus');
        }

        // Live Search Tabel
        function filterTabel() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let rows = document.querySelectorAll('#tabelPesanan tbody tr.item-row');

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                if (text.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>
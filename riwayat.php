<?php include 'includes/header.php';

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role'];

// ─── Filter ───────────────────────────────────────────────────────────────────
$filter_jenis    = isset($_GET['jenis'])     ? input($_GET['jenis'])     : '';
$filter_teknisi  = isset($_GET['teknisi'])   ? input($_GET['teknisi'])   : '';
$filter_dari     = isset($_GET['dari'])      ? input($_GET['dari'])      : '';
$filter_sampai   = isset($_GET['sampai'])    ? input($_GET['sampai'])    : '';
$filter_divisi   = isset($_GET['divisi'])    ? input($_GET['divisi'])    : '';

// Build WHERE clause
$where_parts = ["t.status = 'selesai'"];
if ($role != 'admin') {
    $where_parts[] = "t.user_id = '$uid'";
}
if (!empty($filter_jenis)) {
    $where_parts[] = "t.jenis = '$filter_jenis'";
}
if (!empty($filter_teknisi)) {
    $where_parts[] = "t.teknisi_nama LIKE '%$filter_teknisi%'";
}
if (!empty($filter_dari)) {
    $where_parts[] = "DATE(t.tgl_selesai) >= '$filter_dari'";
}
if (!empty($filter_sampai)) {
    $where_parts[] = "DATE(t.tgl_selesai) <= '$filter_sampai'";
}
if (!empty($filter_divisi)) {
    $where_parts[] = "u.divisi = '$filter_divisi'";
}
$where_sql = implode(' AND ', $where_parts);

// Query
$res = mysqli_query($conn, "SELECT t.*, u.username, u.divisi 
                             FROM tickets t 
                             JOIN users u ON t.user_id = u.id 
                             WHERE $where_sql 
                             ORDER BY t.tgl_selesai DESC");

// Statistik ringkasan
$total_selesai = mysqli_num_rows($res);
mysqli_data_seek($res, 0);

$stat_hardware = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tickets WHERE status='selesai' AND jenis='hardware'"))['t'];
$stat_software = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tickets WHERE status='selesai' AND jenis='software'"))['t'];
$stat_jaringan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tickets WHERE status='selesai' AND jenis='jaringan'"))['t'];

// Daftar divisi untuk filter (admin)
$list_divisi = mysqli_query($conn, "SELECT DISTINCT divisi FROM users WHERE divisi IS NOT NULL ORDER BY divisi");
?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Riwayat Tiket</h2>
        <p class="text-slate-500 text-sm font-medium mt-1">Semua tiket yang telah <span class="text-emerald-600 font-bold">diselesaikan</span> tercatat di sini.</p>
    </div>
    <div class="flex gap-3">
        <?php if ($role == 'admin'): ?>
            <!-- Export dengan filter aktif -->
            <?php
            $export_params = http_build_query([
                'riwayat'  => '1',
                'jenis'    => $filter_jenis,
                'teknisi'  => $filter_teknisi,
                'dari'     => $filter_dari,
                'sampai'   => $filter_sampai,
                'divisi'   => $filter_divisi,
            ]);
            ?>
            <a href="export_excel.php?<?= $export_params ?>" class="bg-emerald-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-emerald-600 transition shadow-lg shadow-emerald-100 flex items-center gap-2">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        <?php endif; ?>
        <a href="index.php" class="bg-slate-100 text-slate-600 px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-slate-200 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Selesai</p>
        <h3 class="text-3xl font-black text-emerald-500"><?= $total_selesai ?></h3>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Hardware</p>
        <h3 class="text-3xl font-black text-amber-500"><?= $stat_hardware ?></h3>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Software</p>
        <h3 class="text-3xl font-black text-indigo-500"><?= $stat_software ?></h3>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Jaringan</p>
        <h3 class="text-3xl font-black text-sky-500"><?= $stat_jaringan ?></h3>
    </div>
</div>

<!-- Filter Panel -->
<div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <i class="fas fa-filter text-indigo-400 text-sm"></i>
        <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Filter Riwayat</h3>
    </div>
    <form method="GET" action="riwayat.php">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-<?= $role == 'admin' ? '5' : '4' ?> gap-4">

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Kategori</label>
                <select name="jenis" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Semua</option>
                    <option value="hardware" <?= $filter_jenis == 'hardware'  ? 'selected' : '' ?>>Hardware</option>
                    <option value="software" <?= $filter_jenis == 'software'  ? 'selected' : '' ?>>Software</option>
                    <option value="jaringan" <?= $filter_jenis == 'jaringan'  ? 'selected' : '' ?>>Jaringan</option>
                </select>
            </div>

            <?php if ($role == 'admin'): ?>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Divisi</label>
                    <select name="divisi" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-700 outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Semua Divisi</option>
                        <?php while ($dv = mysqli_fetch_assoc($list_divisi)): ?>
                            <option value="<?= $dv['divisi'] ?>" <?= $filter_divisi == $dv['divisi'] ? 'selected' : '' ?>><?= $dv['divisi'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Teknisi</label>
                    <input type="text" name="teknisi" value="<?= htmlspecialchars($filter_teknisi) ?>" placeholder="Nama teknisi..."
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm text-slate-700 outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Dari Tanggal</label>
                <input type="date" name="dari" value="<?= $filter_dari ?>"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm text-slate-700 outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Sampai Tanggal</label>
                <input type="date" name="sampai" value="<?= $filter_sampai ?>"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm text-slate-700 outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
        </div>

        <div class="flex gap-3 mt-5">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                <i class="fas fa-search mr-1.5"></i> Terapkan Filter
            </button>
            <a href="riwayat.php" class="bg-slate-100 text-slate-600 px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                <i class="fas fa-times mr-1.5"></i> Reset
            </a>
            <?php
            $has_filter = !empty($filter_jenis) || !empty($filter_teknisi) || !empty($filter_dari) || !empty($filter_sampai) || !empty($filter_divisi);
            if ($has_filter):
            ?>
                <span class="flex items-center gap-1.5 text-xs text-indigo-600 font-bold bg-indigo-50 px-4 py-2.5 rounded-xl">
                    <i class="fas fa-info-circle"></i> <?= $total_selesai ?> hasil ditemukan
                </span>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Riwayat -->
<div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-12">
    <div class="p-6 border-b border-slate-50 flex items-center justify-between">
        <h3 class="font-extrabold text-base text-slate-800">
            <i class="fas fa-history text-emerald-500 mr-2"></i>
            Log Tiket Selesai
            <span class="ml-2 text-xs bg-emerald-100 text-emerald-600 px-2.5 py-1 rounded-full font-black"><?= $total_selesai ?> tiket</span>
        </h3>
    </div>

    <!-- Scrollable -->
    <div class="overflow-x-auto">
        <div class="overflow-y-auto" style="max-height: 520px;">
            <table class="w-full text-left">
                <thead class="sticky top-0 z-10">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4">ID Tiket</th>
                        <th class="px-6 py-4">Pelapor / Divisi</th>
                        <th class="px-6 py-4">PC & Keluhan</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Teknisi</th>
                        <th class="px-6 py-4">Tgl Selesai</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (mysqli_num_rows($res) == 0): ?>
                        <tr>
                            <td colspan="7" class="px-8 py-16 text-center text-slate-400">
                                <i class="fas fa-box-open text-4xl mb-3 block text-slate-200"></i>
                                <p class="text-sm font-semibold">Belum ada riwayat tiket selesai.</p>
                                <?php if ($has_filter): ?>
                                    <p class="text-xs mt-1">Coba ubah atau <a href="riwayat.php" class="text-indigo-500 underline">reset filter</a>.</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php else: while ($row = mysqli_fetch_assoc($res)):
                            $badge_jenis = 'bg-slate-100 text-slate-600';
                            if ($row['jenis'] == 'hardware') $badge_jenis = 'bg-amber-100 text-amber-600';
                            if ($row['jenis'] == 'software') $badge_jenis = 'bg-indigo-100 text-indigo-600';
                            if ($row['jenis'] == 'jaringan') $badge_jenis = 'bg-sky-100 text-sky-600';
                        ?>
                            <tr class="hover:bg-emerald-50/40 transition-colors group">
                                <td class="px-6 py-5">
                                    <span class="font-black text-emerald-600 text-sm">#TKT-<?= $row['id'] ?></span>
                                    <p class="text-[10px] text-slate-400 font-bold mt-0.5"><?= date('d M Y', strtotime($row['created_at'])) ?></p>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-bold text-slate-700"><?= htmlspecialchars($row['username']) ?></p>
                                    <p class="text-[10px] text-slate-400"><?= htmlspecialchars($row['divisi']) ?></p>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-xs font-extrabold text-slate-800"><?= $row['no_pc'] ?></p>
                                    <p class="text-xs text-slate-500 truncate max-w-[180px] mt-0.5"><?= htmlspecialchars($row['keluhan']) ?></p>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="<?= $badge_jenis ?> text-[10px] font-black uppercase px-3 py-1.5 rounded-lg"><?= $row['jenis'] ?></span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-xs font-black">
                                            <?= strtoupper(substr($row['teknisi_nama'] ?? '?', 0, 1)) ?>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-700"><?= htmlspecialchars($row['teknisi_nama'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <?php if (!empty($row['tgl_selesai'])): ?>
                                        <p class="text-xs font-bold text-slate-700"><?= date('d M Y', strtotime($row['tgl_selesai'])) ?></p>
                                        <p class="text-[10px] text-slate-400"><?= date('H:i', strtotime($row['tgl_selesai'])) ?> WIB</p>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button onclick='viewDetail(<?= json_encode($row) ?>)'
                                        class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-emerald-500 hover:text-white transition-all flex items-center justify-center ml-auto"
                                        title="Lihat Detail">
                                        <i class="fas fa-expand-alt text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                    <?php endwhile;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Tiket -->
<div id="modalDetail" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-xl p-8 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black text-emerald-600" id="detailTitle"></h3>
            <button onclick="closeDetail()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:bg-slate-200 flex items-center justify-center transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="space-y-4 text-sm" id="detailBody"></div>
        <button onclick="closeDetail()" class="w-full mt-6 py-3 rounded-2xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-all">
            Tutup
        </button>
    </div>
</div>

<script>
    function viewDetail(data) {
        document.getElementById('detailTitle').textContent = 'TICKET #' + data.id + ' — SELESAI';
        const buktiBefore = data.bukti_keluhan ?
            `<img src="uploads/${data.bukti_keluhan}" class="rounded-xl w-full h-32 object-cover">` :
            `<div class="h-32 bg-slate-100 flex items-center justify-center rounded-xl text-slate-400 italic text-xs">Belum ada</div>`;
        const buktiAfter = data.bukti_selesai ?
            `<img src="uploads/${data.bukti_selesai}" class="rounded-xl w-full h-32 object-cover">` :
            `<div class="h-32 bg-slate-100 flex items-center justify-center rounded-xl text-slate-400 italic text-xs">Belum ada</div>`;
        const tglSelesai = data.tgl_selesai ? new Date(data.tgl_selesai).toLocaleString('id-ID') : '-';

        document.getElementById('detailBody').innerHTML = `
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 bg-slate-50 rounded-xl"><span class="text-[10px] font-black text-slate-400 uppercase block mb-1">Device</span><span class="font-bold text-slate-800">${data.no_pc}</span></div>
                <div class="p-3 bg-emerald-50 rounded-xl"><span class="text-[10px] font-black text-emerald-400 uppercase block mb-1">Status</span><span class="font-bold text-emerald-600">✓ SELESAI</span></div>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl"><span class="text-[10px] font-black text-slate-400 uppercase block mb-1">Keluhan Awal</span><p class="text-slate-700">${data.keluhan}</p></div>
            <div class="p-3 bg-indigo-50 rounded-xl"><span class="text-[10px] font-black text-indigo-400 uppercase block mb-1">Solusi IT oleh ${data.teknisi_nama || '-'}</span><p class="text-indigo-700">${data.keterangan_it || '-'}</p></div>
            <div class="p-3 bg-slate-50 rounded-xl text-center"><span class="text-[10px] font-black text-slate-400 uppercase block mb-1">Diselesaikan Pada</span><span class="font-bold text-slate-800 text-xs">${tglSelesai}</span></div>
            <div class="grid grid-cols-2 gap-3">
                <div><p class="text-[10px] font-black text-slate-400 uppercase mb-2">Bukti User</p>${buktiBefore}</div>
                <div><p class="text-[10px] font-black text-slate-400 uppercase mb-2">Bukti IT</p>${buktiAfter}</div>
            </div>`;
        document.getElementById('modalDetail').classList.remove('hidden');
    }

    function closeDetail() {
        document.getElementById('modalDetail').classList.add('hidden');
    }
    document.getElementById('modalDetail').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });
</script>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/header.php';

// 1. Data Grafik Batang (Tiket 7 Hari Terakhir)
$label_hari = [];
$data_jumlah_tiket = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $label_hari[] = date('D', strtotime($tgl));
    $sql_harian = mysqli_query($conn, "SELECT COUNT(*) as total FROM tickets WHERE DATE(created_at) = '$tgl'");
    $res_harian = mysqli_fetch_assoc($sql_harian);
    $data_jumlah_tiket[] = (int)$res_harian['total'];
}

// 2. Data Pie Chart (Jenis Gangguan)
$jenis_labels = ['Software', 'Hardware', 'Jaringan', 'Lainnya'];
$data_jenis = [];
foreach ($jenis_labels as $j) {
    $sql_j = mysqli_query($conn, "SELECT COUNT(*) as total FROM tickets WHERE jenis = '$j'");
    $res_j = mysqli_fetch_assoc($sql_j);
    $data_jenis[] = (int)$res_j['total'];
}

// 3. Top Reporter (Divisi Paling Sering Lapor)
$sql_divisi = mysqli_query($conn, "SELECT u.divisi, COUNT(t.id) as total 
                                   FROM tickets t 
                                   JOIN users u ON t.user_id = u.id 
                                   GROUP BY u.divisi 
                                   ORDER BY total DESC LIMIT 5");

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Statistik Keseluruhan
$s1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tickets WHERE status='Antrian'"))['t'];
$s2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tickets WHERE status='proses'"))['t'];
$s3 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tickets WHERE status='selesai'"))['t'];
?>


<?php if ($_SESSION['role'] == 'user') : ?>
    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[2rem] p-8 mb-8 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Halo, <?= $_SESSION['username'] ?>! 👋</h2>
            <p class="text-indigo-100 text-sm max-w-md">
                Selamat datang di SmartHelp. Klik tombol <b>"Buat Request"</b> untuk melaporkan kendala IT Anda, atau cek status perbaikan pada tabel di bawah.
            </p>
        </div>
        <i class="fas fa-rocket absolute -right-4 -bottom-4 text-8xl text-white/10 -rotate-12"></i>
    </div>
<?php endif; ?>

<!-- Header Dashboard -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Main Dashboard</h2>
        <p class="text-slate-500 text-sm font-medium mt-1">Pantau performa hardware & software secara real-time.</p>
    </div>
    <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse ml-2"></div>
        <span class="text-xs font-bold text-slate-600 mr-2 uppercase tracking-widest">Live Monitoring</span>
    </div>
</div>

<!-- Kartu Statistik -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-[2rem] shadow-xl shadow-orange-500/20 text-white relative overflow-hidden">
        <div class="flex items-center justify-between relative z-10">
            <div>
                <p class="text-orange-100 text-xs font-bold uppercase tracking-widest mb-1">Tiket Baru</p>
                <h3 class="text-4xl font-black"><?= $s1 ?></h3>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-envelope-open-text"></i>
            </div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 rounded-[2rem] shadow-xl shadow-indigo-500/20 text-white relative overflow-hidden">
        <div class="flex items-center justify-between relative z-10">
            <div>
                <p class="text-indigo-100 text-xs font-bold uppercase tracking-widest mb-1">Proses</p>
                <h3 class="text-4xl font-black"><?= $s2 ?></h3>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 rounded-[2rem] shadow-xl shadow-emerald-500/20 text-white relative overflow-hidden">
        <div class="flex items-center justify-between relative z-10">
            <div>
                <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest mb-1">Selesai</p>
                <h3 class="text-4xl font-black"><?= $s3 ?></h3>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Tiket Aktif (Antrian & Proses) -->
<div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-12">
    <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="font-extrabold text-lg text-slate-800">Daftar Laporan Aktif</h3>
            <p class="text-xs text-slate-400 mt-0.5 font-medium">Menampilkan tiket dengan status <span class="text-orange-500 font-bold">Antrian</span> & <span class="text-blue-500 font-bold">Proses</span>. Tiket selesai ada di <a href="riwayat.php" class="text-indigo-600 underline font-bold">Riwayat</a>.</p>
        </div>
        <?php if ($role == 'admin'): ?>
            <a href="export_excel.php" class="bg-emerald-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-emerald-600 transition shadow-lg shadow-emerald-100">
                <i class="fas fa-file-excel mr-2"></i> Export Excel
            </a>
        <?php endif; ?>
    </div>

    <!-- Scrollable Table Wrapper -->
    <div class="overflow-x-auto">
        <div class="overflow-y-auto" style="max-height: 480px;">
            <table class="w-full text-left">
                <thead class="sticky top-0 z-10">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 border-b border-slate-100">
                        <th class="px-8 py-5">Request ID</th>
                        <th class="px-8 py-5">Kendala / PC</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php
                    // Hanya tampilkan tiket AKTIF (bukan selesai)
                    if ($role == 'admin') {
                        $q = "SELECT t.*, u.username FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.status != 'selesai' ORDER BY t.id DESC";
                    } else {
                        $q = "SELECT t.*, u.username FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.user_id = '$uid' AND t.status != 'selesai' ORDER BY t.id DESC";
                    }
                    $res = mysqli_query($conn, $q);
                    $row_count = mysqli_num_rows($res);

                    if ($row_count == 0):
                    ?>
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center text-slate-400">
                                <i class="fas fa-inbox text-4xl mb-3 block text-slate-200"></i>
                                <p class="text-sm font-semibold">Tidak ada tiket aktif saat ini.</p>
                                <p class="text-xs mt-1">Semua tiket mungkin sudah selesai. Cek <a href="riwayat.php" class="text-indigo-500 underline font-bold">halaman Riwayat</a>.</p>
                            </td>
                        </tr>
                        <?php else: while ($row = mysqli_fetch_assoc($res)):
                            $lbl = "bg-orange-100 text-orange-600 ring-orange-200";
                            if ($row['status'] == 'proses') $lbl = "bg-blue-100 text-blue-600 ring-blue-200";
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-8 py-6">
                                    <span class="font-black text-indigo-600 text-sm">#TKT-<?= $row['id'] ?></span>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase"><?= date('D, d M Y', strtotime($row['created_at'])) ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-extrabold text-slate-800"><?= $row['no_pc'] ?></span>
                                        <?php if ($row['urgensi'] == 'urgent'): ?>
                                            <span class="bg-red-100 text-red-600 text-[9px] px-2 py-0.5 rounded-full font-black uppercase tracking-tighter animate-pulse">Urgent</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-slate-500 truncate w-48"><?= $row['keluhan'] ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="<?= $lbl ?> text-[10px] font-black uppercase px-3 py-1.5 rounded-xl ring-1 shadow-sm"><?= $row['status'] ?></span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <!-- Tombol Detail -->
                                        <button onclick='viewDetail(<?= json_encode($row) ?>)'
                                            class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-600 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center"
                                            title="Lihat Detail">
                                            <i class="fas fa-expand-alt text-sm"></i>
                                        </button>

                                        <?php if ($role == 'admin'): ?>
                                            <!-- Tombol Update (Admin) -->
                                            <button onclick='openUpdate(<?= json_encode($row) ?>)'
                                                class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center"
                                                title="Update Status">
                                                <i class="fas fa-pencil-alt text-sm"></i>
                                            </button>
                                            <!-- Tombol Hapus (Admin) -->
                                            <button onclick="confirmDelete('<?= $row['id'] ?>', 'ticket')"
                                                class="w-10 h-10 rounded-2xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center"
                                                title="Hapus Tiket">
                                                <i class="fas fa-trash-alt text-sm"></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($role == 'user' && $row['status'] == 'Antrian'): ?>
                                            <!-- Tombol Hapus (User, hanya jika masih Antrian) -->
                                            <button onclick="confirmDelete('<?= $row['id'] ?>', 'ticket')"
                                                class="w-10 h-10 rounded-2xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center"
                                                title="Batalkan Tiket">
                                                <i class="fas fa-trash-alt text-sm"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                    <?php endwhile;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Chart Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Tren Tiket (7 Hari Terakhir)</h3>
        <canvas id="chartMingguan" height="200"></canvas>
    </div>
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Kategori Kendala</h3>
        <div class="max-w-[220px] mx-auto">
            <canvas id="chartKategori"></canvas>
        </div>
    </div>
</div>

<!-- Top Divisi -->
<div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-12">
    <h3 class="text-sm font-bold text-slate-800 mb-6">Top 5 Divisi (Pelapor Terbanyak)</h3>
    <div class="space-y-6">
        <?php
        $total_semua_tiket = array_sum($data_jumlah_tiket) ?: 1;
        while ($d = mysqli_fetch_assoc($sql_divisi)):
            $persen = ($d['total'] / $total_semua_tiket) * 100;
        ?>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 font-semibold w-24"><?= $d['divisi'] ?></span>
                <div class="flex-1 mx-4 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="bg-indigo-500 h-full rounded-full" style="width: <?= $persen ?>%"></div>
                </div>
                <span class="text-xs font-black text-slate-800"><?= $d['total'] ?> Tiket</span>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal Detail Tiket -->
<div id="modalDetail" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-xl p-8 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black text-indigo-600" id="detailTitle"></h3>
            <button onclick="closeDetail()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:bg-slate-200 flex items-center justify-center transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="space-y-4 text-sm" id="detailBody"></div>
        <button onclick="closeDetail()" class="w-full mt-6 py-3 rounded-2xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all">
            Tutup
        </button>
    </div>
</div>

<!-- Modal Update Tiket -->
<div id="modalUpdate" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Update Penanganan</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:bg-slate-200 flex items-center justify-center transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form action="process_crud.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="id" id="upd_id">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Teknisi Penanggung Jawab</label>
                <input type="text" name="teknisi" id="upd_teknisi" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Status Tiket</label>
                <select name="status" id="upd_status" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="Antrian">Antrian</option>
                    <option value="proses">Proses</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Keterangan Perbaikan</label>
                <textarea name="keterangan_it" id="upd_keterangan" rows="3" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>
            <div id="bukti_container" class="hidden bg-indigo-50 p-4 rounded-2xl border border-indigo-100">
                <label class="block text-[10px] font-black text-indigo-400 uppercase mb-2">Upload Bukti Selesai</label>
                <input type="file" name="bukti_selesai" class="text-xs text-slate-500 cursor-pointer">
            </div>
            <button type="submit" name="update_admin" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-indigo-700 transition-all mt-4">
                Simpan & Update
            </button>
        </form>
    </div>
</div>

<script>
    // ─── Chart Mingguan ───────────────────────────────────────────────────────
    new Chart(document.getElementById('chartMingguan'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($label_hari) ?>,
            datasets: [{
                label: 'Jumlah Tiket',
                data: <?= json_encode($data_jumlah_tiket) ?>,
                backgroundColor: '#6366f1',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // ─── Chart Kategori ───────────────────────────────────────────────────────
    new Chart(document.getElementById('chartKategori'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($jenis_labels) ?>,
            datasets: [{
                data: <?= json_encode($data_jenis) ?>,
                backgroundColor: ['#6366f1', '#f59e0b', '#10b981', '#94a3b8']
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // ─── Modal Detail Tiket ───────────────────────────────────────────────────
    function viewDetail(data) {
        document.getElementById('detailTitle').textContent = 'TICKET #' + data.id;

        const buktiBefore = data.bukti_keluhan ?
            `<img src="uploads/${data.bukti_keluhan}" class="rounded-xl w-full h-32 object-cover">` :
            `<div class="h-32 bg-slate-100 flex items-center justify-center rounded-xl text-slate-400 italic text-xs">Belum ada</div>`;
        const buktiAfter = data.bukti_selesai ?
            `<img src="uploads/${data.bukti_selesai}" class="rounded-xl w-full h-32 object-cover">` :
            `<div class="h-32 bg-slate-100 flex items-center justify-center rounded-xl text-slate-400 italic text-xs">Belum ada</div>`;

        document.getElementById('detailBody').innerHTML = `
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 bg-slate-50 rounded-xl"><span class="text-[10px] font-black text-slate-400 uppercase block mb-1">Device</span><span class="font-bold text-slate-800">${data.no_pc}</span></div>
                <div class="p-3 bg-slate-50 rounded-xl"><span class="text-[10px] font-black text-slate-400 uppercase block mb-1">Status</span><span class="font-bold text-slate-800">${data.status}</span></div>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl"><span class="text-[10px] font-black text-slate-400 uppercase block mb-1">Keluhan</span><p class="text-slate-700">${data.keluhan}</p></div>
            <div class="p-3 bg-indigo-50 rounded-xl"><span class="text-[10px] font-black text-indigo-400 uppercase block mb-1">Solusi IT</span><p class="text-indigo-700">${data.keterangan_it || '-'}</p></div>
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

    // ─── Modal Update Tiket ───────────────────────────────────────────────────
    function openUpdate(data) {
        document.getElementById('upd_id').value = data.id;
        document.getElementById('upd_teknisi').value = data.teknisi_nama || '';
        document.getElementById('upd_status').value = data.status;
        document.getElementById('upd_keterangan').value = data.keterangan_it || '';
        document.getElementById('modalUpdate').classList.remove('hidden');

        const statusSelect = document.getElementById('upd_status');
        const buktiCont = document.getElementById('bukti_container');
        const toggleBukti = () => statusSelect.value === 'selesai' ?
            buktiCont.classList.remove('hidden') :
            buktiCont.classList.add('hidden');

        toggleBukti();
        statusSelect.onchange = toggleBukti;
    }

    function closeModal() {
        document.getElementById('modalUpdate').classList.add('hidden');
    }

    document.getElementById('modalUpdate').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>

<?php include 'includes/footer.php'; ?>
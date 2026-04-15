<?php include 'includes/header.php';
proteksi_halaman();
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$divisi_list   = mysqli_query($conn, "SELECT DISTINCT divisi FROM users WHERE divisi IS NOT NULL");
$filter_divisi = isset($_GET['filter_divisi']) ? input($_GET['filter_divisi']) : '';

$q_str = "SELECT * FROM users";
if ($filter_divisi != '') $q_str .= " WHERE divisi = '$filter_divisi'";
$q_str .= " ORDER BY id DESC";
$res = mysqli_query($conn, $q_str);
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
    <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">Kelola Pengguna</h2>
        <p class="text-slate-500 text-sm mt-1">Manajemen akses akun staf dan teknisi berdasarkan divisi.</p>
    </div>
    <button onclick="openModalAdd()"
        class="w-full sm:w-auto bg-indigo-600 text-white px-5 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all flex items-center justify-center gap-2 text-sm">
        <i class="fas fa-user-plus"></i> Tambah Pengguna
    </button>
</div>

<div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-5 flex flex-wrap items-center gap-3">
    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter Divisi:</span>
    <form method="GET">
        <select name="filter_divisi" onchange="this.form.submit()"
            class="bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Semua Divisi</option>
            <?php while ($d = mysqli_fetch_assoc($divisi_list)): ?>
                <option value="<?= $d['divisi'] ?>" <?= $filter_divisi == $d['divisi'] ? 'selected' : '' ?>>
                    <?= $d['divisi'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>
</div>

<div class="hidden md:block bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-6">
    <div class="overflow-x-auto overflow-y-auto max-h-[500px] custom-scrollbar">
        <table class="w-full min-w-[560px] text-left border-separate border-spacing-0">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/90 backdrop-blur-sm sticky top-0 z-10">
                    <th class="px-6 py-4 border-b border-slate-100">Username</th>
                    <th class="px-6 py-4 border-b border-slate-100">Divisi</th>
                    <th class="px-6 py-4 border-b border-slate-100">Role</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50" id="tableBodyDesktop">
                <?php
                mysqli_data_seek($res, 0);
                while ($row = mysqli_fetch_assoc($res)):
                ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-black shrink-0">
                                    <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                </div>
                                <span class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($row['username']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-slate-100 text-slate-600 text-[10px] px-3 py-1 rounded-lg font-bold uppercase">
                                <?= htmlspecialchars($row['divisi']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="<?= $row['role'] == 'admin' ? 'bg-indigo-100 text-indigo-600' : 'bg-emerald-100 text-emerald-600' ?> text-[10px] px-3 py-1 rounded-lg font-black uppercase">
                                <?= $row['role'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    onclick="openModalEdit('<?= $row['id'] ?>', '<?= htmlspecialchars($row['username'], ENT_QUOTES) ?>', '<?= $row['role'] ?>', '<?= htmlspecialchars($row['divisi'], ENT_QUOTES) ?>')"
                                    class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-all"
                                    title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button
                                    onclick="confirmDelete('<?= $row['id'] ?>', 'user')"
                                    class="w-9 h-9 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all"
                                    title="Hapus">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="md:hidden overflow-y-auto max-h-[600px] pr-1 space-y-3 mb-6 custom-scrollbar">
    <?php
    mysqli_data_seek($res, 0);
    while ($row = mysqli_fetch_assoc($res)):
    ?>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-black text-base shrink-0">
                <?= strtoupper(substr($row['username'], 0, 1)) ?>
            </div>

            <div class="flex-1 min-w-0">
                <p class="font-bold text-slate-800 text-sm truncate"><?= htmlspecialchars($row['username']) ?></p>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    <span class="bg-slate-100 text-slate-500 text-[9px] px-2 py-0.5 rounded-md font-bold uppercase">
                        <?= htmlspecialchars($row['divisi']) ?>
                    </span>
                    <span class="<?= $row['role'] == 'admin' ? 'bg-indigo-100 text-indigo-600' : 'bg-emerald-100 text-emerald-600' ?> text-[9px] px-2 py-0.5 rounded-md font-black uppercase">
                        <?= $row['role'] ?>
                    </span>
                </div>
            </div>

            <div class="flex gap-2 shrink-0">
                <button
                    onclick="openModalEdit('<?= $row['id'] ?>', '<?= htmlspecialchars($row['username'], ENT_QUOTES) ?>', '<?= $row['role'] ?>', '<?= htmlspecialchars($row['divisi'], ENT_QUOTES) ?>')"
                    class="w-9 h-9 rounded-xl bg-amber-50 text-amber-500 active:bg-amber-500 active:text-white flex items-center justify-center transition-all">
                    <i class="fas fa-edit text-xs"></i>
                </button>
                <button
                    onclick="confirmDelete('<?= $row['id'] ?>', 'user')"
                    class="w-9 h-9 rounded-xl bg-red-50 text-red-500 active:bg-red-500 active:text-white flex items-center justify-center transition-all">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<style>
    /* Styling scrollbar agar lebih tipis dan modern */
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

<div id="modalUser" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-[2rem] sm:rounded-[2rem] p-6 sm:p-8 shadow-2xl border border-slate-100 max-h-[92vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Tambah Pengguna</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:bg-slate-200 flex items-center justify-center transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto mb-4"></div>
        <form action="process_crud.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Username</label>
                <input type="text" name="username" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Password</label>
                <input type="password" name="password" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Role</label>
                    <select name="role" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="user">User (Staf)</option>
                        <option value="admin">Admin (IT)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Divisi</label>
                    <input type="text" name="divisi" placeholder="HRD, FINANCE..." class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>
            </div>
            <button type="submit" name="add_user" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all mt-2">
                <i class="fas fa-user-plus mr-2"></i> Simpan Pengguna Baru
            </button>
            <button type="button" onclick="closeModal()" class="w-full text-slate-400 text-xs font-bold py-2">Batal</button>
        </form>
    </div>
</div>

<div id="modalEditUser" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-[2rem] sm:rounded-[2rem] p-6 sm:p-8 shadow-2xl border border-slate-100 max-h-[92vh] overflow-y-auto">
        <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto mb-4"></div>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Edit Pengguna</h3>
            <button onclick="closeModalEdit()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:bg-slate-200 flex items-center justify-center transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form action="process_crud.php" method="POST" class="space-y-4">
            <input type="hidden" name="id" id="edit_id">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Username</label>
                <input type="text" name="username" id="edit_username" class="w-full bg-slate-100 border border-slate-100 rounded-xl p-3 text-sm outline-none cursor-not-allowed" readonly>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Role</label>
                    <select name="role" id="edit_role" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="user">User (Staf)</option>
                        <option value="admin">Admin (IT)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Divisi</label>
                    <input type="text" name="divisi" id="edit_divisi" class="w-full bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>
            </div>
            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
                <p class="text-[10px] text-amber-700 mb-2"><i class="fas fa-info-circle mr-1"></i> Biarkan kosong jika tidak ingin mengubah password.</p>
                <input type="password" name="password" placeholder="Password Baru (Opsional)" class="w-full bg-white border border-amber-200 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-amber-300">
            </div>
            <button type="submit" name="update_user" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all mt-2">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
            <button type="button" onclick="closeModalEdit()" class="w-full text-slate-400 text-xs font-bold py-2">Batal</button>
        </form>
    </div>
</div>

<script>
    // Pastikan fungsi ini tersedia secara global
    function confirmDelete(id, type) {
        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
            // Menggunakan delete_user_id sesuai logic PHP yang diperbaiki tadi
            window.location.href = 'process_crud.php?delete_user_id=' + id;
        }
    }

    function openModalAdd() {
        document.getElementById('modalUser').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalUser').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openModalEdit(id, username, role, divisi) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_divisi').value = divisi;
        document.getElementById('modalEditUser').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalEdit() {
        document.getElementById('modalEditUser').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close modals on background click
    window.onclick = function(event) {
        if (event.target.id === 'modalUser') closeModal();
        if (event.target.id === 'modalEditUser') closeModalEdit();
    }
</script>
<?php include 'includes/footer.php'; ?>
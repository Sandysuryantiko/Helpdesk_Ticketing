<?php
include 'includes/header.php';
$divisi_user = isset($_SESSION['divisi']) ? $_SESSION['divisi'] : 'Umum';
?>

<div class="max-w-3xl mx-auto mt-10 p-4">
    <a href="index.php"
        class="inline-flex items-center text-slate-400 hover:text-indigo-600 font-bold text-xs uppercase tracking-widest transition-colors mb-6 group">
        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali ke Dashboard
    </a>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <div class="bg-slate-900 p-8 md:p-12 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <i class="fas fa-ticket-alt text-9xl -rotate-12"></i>
            </div>
            <div class="relative z-10">
                <h2 class="text-3xl font-black tracking-tight mb-2">Buat Request Baru</h2>
                <p class="text-slate-400 text-sm font-medium">Sistem akan otomatis mencatat ID Anda dan meneruskannya ke antrian teknisi.</p>
            </div>
        </div>

        <form action="process_crud.php" method="POST" enctype="multipart/form-data" class="p-8 md:p-12 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Divisi Pelapor</label>
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 px-5 py-4 rounded-2xl">
                        <i class="fas fa-building text-indigo-500"></i>
                        <span class="font-bold text-slate-700"><?= $divisi_user ?></span>
                        <input type="hidden" name="divisi" value="<?= $divisi_user ?>">
                    </div>
                    <p class="text-[10px] text-slate-400 ml-1 italic"><i class="fas fa-info-circle mr-1"></i> Otomatis sesuai profil Anda.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Nomor / Nama PC</label>
                    <div class="relative">
                        <i class="fas fa-desktop absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="no_pc"
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 pl-12 pr-5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all appearance-none"
                            required>
                            <option value="">Pilih Perangkat...</option>
                            <option value="PC-01">PC-01 (<?= $divisi_user ?>)</option>
                            <option value="PC-02">PC-02 (<?= $divisi_user ?>)</option>
                            <option value="PC-03">PC-03 (<?= $divisi_user ?>)</option>
                            <option value="LAPTOP-ADMIN">LAPTOP-ADMIN</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none text-xs"></i>
                    </div>
                    <p class="text-[10px] text-slate-400 ml-1 italic"><i class="fas fa-tag mr-1"></i> Pilih ID PC yang tertempel pada casing/laptop.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Kategori Masalah</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="hardware" class="peer hidden" checked>
                            <div class="text-center p-3 rounded-xl border border-slate-100 bg-slate-50 text-[10px] font-bold uppercase text-slate-500 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all">Hardware</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="software" class="peer hidden">
                            <div class="text-center p-3 rounded-xl border border-slate-100 bg-slate-50 text-[10px] font-bold uppercase text-slate-500 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all">Software</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="jaringan" class="peer hidden">
                            <div class="text-center p-3 rounded-xl border border-slate-100 bg-slate-50 text-[10px] font-bold uppercase text-slate-500 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all">Network</div>
                        </label>
                    </div>
                    <p class="text-[10px] text-slate-400 ml-1 italic"><i class="fas fa-tools mr-1"></i> Pilih tipe kerusakan yang dirasakan.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Prioritas (Urgensi)</label>
                    <select name="urgensi"
                        class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <option value="biasa">Normal (Biasa)</option>
                        <option value="urgent">Urgent (Pekerjaan Terhenti)</option>
                    </select>
                    <p class="text-[10px] text-slate-400 ml-1 italic"><i class="fas fa-exclamation-triangle mr-1"></i> Gunakan 'Urgent' hanya jika pekerjaan terhenti total.</p>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Jelaskan Masalah Anda secara Detail</label>
                <textarea name="keluhan" rows="4"
                    class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-5 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all"
                    placeholder="Contoh: Monitor tidak menyala setelah ada petir, atau aplikasi kantor error saat dibuka..."
                    required></textarea>
                <p class="text-[10px] text-slate-400 ml-1 italic"><i class="fas fa-lightbulb mr-1"></i> Semakin detail penjelasan, semakin cepat teknisi mendiagnosa.</p>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Foto Bukti (Wajib)</label>
                <div class="relative group">
                    <div class="absolute inset-0 bg-indigo-600/5 rounded-2xl border-2 border-dashed border-indigo-200 group-hover:bg-indigo-600/10 transition-colors"></div>
                    <input type="file" name="bukti" class="relative z-10 w-full h-32 opacity-0 cursor-pointer"
                        accept="image/*" required id="fileInput">
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-indigo-400">
                        <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest" id="fileName">Klik atau seret foto ke sini</span>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 ml-1 italic"><i class="fas fa-camera mr-1"></i> Upload screenshot error atau foto kondisi fisik perangkat.</p>
            </div>

            <div class="pt-6">
                <button type="submit" name="simpan_tiket"
                    class="w-full bg-indigo-600 text-white text-sm font-black uppercase tracking-widest py-5 rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 active:scale-95 transition-all">
                    Kirim ke Antrian IT <i class="fas fa-paper-plane ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview nama file yang dipilih
    document.getElementById('fileInput').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            document.getElementById('fileName').textContent = 'File Terpilih: ' + e.target.files[0].name;
            document.getElementById('fileName').classList.add('text-emerald-500');
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
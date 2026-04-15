<?php include 'includes/header.php'; ?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-slate-900">Panduan Pengguna</h2>
        <p class="text-slate-500 text-sm">Pelajari cara menggunakan SmartHelp untuk mempercepat penanganan kendala Anda.</p>
    </div>

    <div class="grid gap-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex gap-6 items-start">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex-shrink-0 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-200">1</div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 text-lg">Buat Request Tiket</h4>
                <p class="text-sm text-slate-600 leading-relaxed">Klik menu <span class="font-bold text-indigo-600">"Buat Request"</span> di sidebar. Isi data PC, pilih kategori kendala, dan pastikan Anda melampirkan foto bukti kendala (screenshot/foto HP) agar teknisi lebih cepat paham.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex gap-6 items-start">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex-shrink-0 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-200">2</div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 text-lg">Pantau Status</h4>
                <p class="text-sm text-slate-600 leading-relaxed">Lihat dashboard Anda. Status <span class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded text-[10px] font-bold">ANTRIAN</span> berarti tiket masuk. Status <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded text-[10px] font-bold">PROSES</span> berarti teknisi sedang menangani masalah Anda.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex gap-6 items-start">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex-shrink-0 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-200">3</div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 text-lg">Tiket Selesai</h4>
                <p class="text-sm text-slate-600 leading-relaxed">Jika sudah <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded text-[10px] font-bold">SELESAI</span>, Anda bisa melihat foto bukti perbaikan dan keterangan dari IT pada detail tiket.</p>
            </div>
        </div>
    </div>

    <div class="mt-12 bg-slate-900 rounded-[2rem] p-8 text-center">
        <h3 class="text-white font-bold text-xl mb-2">Butuh Bantuan Darurat?</h3>
        <p class="text-slate-400 text-sm mb-6">Hubungi IT Support via WhatsApp jika sistem mengalami kendala total.</p>
        <a href="https://wa.me/62812345678" target="_blank" class="inline-flex items-center gap-2 bg-green-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-600 transition">
            <i class="fab fa-whatsapp"></i> Chat WhatsApp IT
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
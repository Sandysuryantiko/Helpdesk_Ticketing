</div>
</main>

<footer class="md:hidden bg-white border-t p-4 text-center text-xs text-gray-400">
    SmartHelp IT &copy; 2026
</footer>

<div id="modalConfirmDelete" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-[200] flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-sm p-8 shadow-2xl border border-slate-100 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-trash-alt text-2xl text-red-500"></i>
        </div>
        <h3 id="deleteModalTitle" class="text-lg font-extrabold text-slate-800 mb-2"></h3>
        <p id="deleteModalText" class="text-sm text-slate-500 mb-8"></p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()"
                class="flex-1 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-sm hover:bg-slate-50 transition-all">
                Batal
            </button>
            <a id="deleteConfirmBtn" href="#"
                class="flex-1 py-3 rounded-2xl bg-red-500 text-white font-bold text-sm hover:bg-red-600 transition-all shadow-lg shadow-red-100">
                Ya, Hapus!
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="https://js.pusher.com/8.0/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/1.0.8/push.min.js"></script>

<script>
    // 1. Inisialisasi Notyf Pertama Kali agar bisa dipakai di mana saja
    const notyf = new Notyf({
        duration: 5000,
        position: {
            x: 'right',
            y: 'top'
        },
        dismissible: true,
        ripple: true,
        types: [{
                type: 'warning',
                background: '#f59e0b',
                icon: {
                    className: 'fas fa-exclamation-triangle',
                    tagName: 'i',
                    color: 'white'
                }
            },
            {
                type: 'info',
                background: '#6366f1',
                icon: {
                    className: 'fas fa-info-circle',
                    tagName: 'i',
                    color: 'white'
                }
            }
        ]
    });

    // 2. --- KONFIGURASI PUSHER & PUSH.JS ---
    const pusher = new Pusher('1143d77caa902710fd33', {
        cluster: 'ap1',
        forceTLS: true
    });

    // Pastikan nama channel SAMA dengan di process_crud.php (helpdesk_chanel)
    const channel = pusher.subscribe('helpdesk_chanel');

    // Minta izin notifikasi desktop
    Push.Permission.request();

    channel.bind('new-ticket', function(data) {
        console.log("Mencoba memicu Push.js..."); // Cek di F12 apakah baris ini jalan/ Untuk cek di Inspect Element > Console

        // TAMBAHKAN INI: Otomatis refresh halaman setelah 2 detik agar tiket baru muncul di tabel
        setTimeout(function() {
            window.location.reload();
        }, 2000);
        // A. Notifikasi Notyf (Dalam Web)
        Push.create("Request IT Baru!", {
            body: data.nama + " mengirim keluhan: " + data.judul,
            icon: 'https://cdn-icons-png.flaticon.com/512/1067/1067564.png', // Gunakan URL gambar asli untuk tes
            timeout: 8000, // Muncul selama 8 detik
            requireInteraction: true, // Notif tidak akan hilang sampai diklik (khusus Chrome)
            onClick: function() {
                window.focus();
                this.close();
            }
        });

        // B. Notifikasi Push.js (Luar Browser)
        Push.create("Request IT Baru!", {
            body: data.nama + ": " + data.judul,
            icon: 'assets/img/icon.png',
            timeout: 10000,
            onClick: function() {
                window.focus();
                window.location.href = "index.php";
                this.close();
            }
        });

        // C. Suara Beep
        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2354/2354-preview.mp3');
        audio.play().catch(e => console.log("Audio play blocked: Interaksi user diperlukan dulu."));
    });


    // 3. Handler Pesan URL (?msg=)
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg) {
        const notifMap = {
            'success': {
                type: 'success',
                text: 'Tiket berhasil dikirim ke antrian IT.'
            },
            'updated': {
                type: 'success',
                text: 'Status tiket berhasil diperbarui.'
            },
            'deleted': {
                type: 'warning',
                text: 'Tiket berhasil dihapus dari sistem.'
            },
            'taken': {
                type: 'info',
                text: 'Tiket berhasil diambil untuk ditangani.'
            },
            'already_taken': {
                type: 'warning',
                text: 'Tiket ini sudah diambil oleh teknisi lain.'
            },
            'missing_file': {
                type: 'error',
                text: 'Foto bukti wajib diunggah sebelum mengirim tiket.'
            },
            'error_delete': {
                type: 'error',
                text: 'Gagal menghapus tiket. Pastikan status masih Antrian.'
            },
            'user_added': {
                type: 'success',
                text: 'Pengguna baru berhasil ditambahkan.'
            },
            'user_updated': {
                type: 'success',
                text: 'Data pengguna berhasil diperbarui.'
            },
            'user_deleted': {
                type: 'warning',
                text: 'Pengguna berhasil dihapus dari sistem.'
            },
            'self_delete_error': {
                type: 'error',
                text: 'Tidak dapat menghapus akun Anda sendiri.'
            },
            'error': {
                type: 'error',
                text: 'Terjadi kesalahan saat memproses data.'
            },
        };

        const notifObj = notifMap[msg];
        if (notifObj) {
            if (notifObj.type === 'success') notyf.success(notifObj.text);
            else if (notifObj.type === 'error') notyf.error(notifObj.text);
            else notyf.open({
                type: notifObj.type,
                message: notifObj.text
            });
        }
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // 4. Fungsi Modal Hapus
    function confirmDelete(id, type) {
        const config = {
            user: {
                url: 'process_crud.php?delete_user=' + id,
                title: 'Hapus Pengguna?',
                text: 'Akun ini akan dihapus permanen dari sistem!'
            },
            ticket: {
                url: 'process_crud.php?delete_id=' + id,
                title: 'Batalkan Request?',
                text: 'Tiket yang dihapus tidak dapat dikembalikan.'
            }
        };
        const c = config[type];
        if (!c) return;
        document.getElementById('deleteModalTitle').textContent = c.title;
        document.getElementById('deleteModalText').textContent = c.text;
        document.getElementById('deleteConfirmBtn').href = c.url;
        document.getElementById('modalConfirmDelete').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('modalConfirmDelete').classList.add('hidden');
    }

    document.getElementById('modalConfirmDelete').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
</body>

</html>
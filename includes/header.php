<?php
include 'core/config.php';
proteksi_halaman();

// Ambil halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);

// Function untuk active menu
function isActive($page)
{
    global $current_page;
    return $current_page == $page
        ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200'
        : 'hover:bg-indigo-600/10 hover:text-indigo-600 text-slate-400';
}

// Function untuk icon active
function activeIcon($page)
{
    global $current_page;
    return $current_page == $page
        ? 'text-white'
        : 'text-indigo-400 group-hover:text-indigo-600';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartHelp | IT Ticketing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Custom Scrollbar untuk Sidebar */
        aside::-webkit-scrollbar {
            width: 4px;
        }

        aside::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }

        /* Custom Notyf */
        .notyf__toast {
            border-radius: 16px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 14px 20px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
        }

        .notyf__toast--success {
            background: #10b981 !important;
        }

        .notyf__toast--error {
            background: #ef4444 !important;
        }

        .notyf__toast--warning {
            background: #f59e0b !important;
        }

        .notyf__icon--success svg,
        .notyf__icon--error svg {
            width: 18px;
            height: 18px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Notyf
            const notyf = new Notyf({
                duration: 5000,
                position: {
                    x: 'right',
                    y: 'top'
                },
                ripple: true
            });

            // 2. Inisialisasi Pusher
            // GANTI 'YOUR_APP_KEY' dengan App Key dari dashboard Pusher Anda
            const pusher = new Pusher('1143d77caa902710fd33', {
                cluster: 'ap1'
            });

            // 3. Subscribe ke Channel (Sesuai Debug Console: helpdesk_chanel)
            const channel = pusher.subscribe('helpdesk_chanel');

            // 4. Bind Event (Sesuai Debug Console: new-ticket)
            channel.bind('new-ticket', function(data) {
                // Tampilkan Notifikasi
                notyf.success(data.message || 'Ada tiket baru masuk!');

                // 5. FITUR AUTO REFRESH (Setelah 2 detik)
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });

            // Debugging di console browser
            pusher.connection.bind('connected', () => {
                console.log('✅ Real-time Dashboard Active');
            });
        });
    </script>
</head>

<body class="bg-[#F8FAFC] text-slate-700 min-h-screen flex flex-col md:flex-row">

    <div class="md:hidden flex items-center justify-between p-4 bg-indigo-600 text-white shadow-lg sticky top-0 z-[60]">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-white/20 rounded-lg"><i class="fas fa-bolt"></i></div>
            <span class="font-extrabold tracking-tight">SmartHelp</span>
        </div>
        <button onclick="toggleMenu()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <aside id="sidebar"
        class="fixed md:sticky top-0 left-0 z-[100] h-screen w-72 bg-slate-900 text-slate-300 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">

        <div class="p-8 flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/50">
                <i class="fas fa-headset text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">SmartHelp</h1>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">IT Solutions</p>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-2 overflow-y-auto">
            <p class="px-4 py-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">Main Menu</p>

            <a href="index.php"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= isActive('index.php') ?>">
                <i class="fas fa-chart-pie text-sm <?= activeIcon('index.php') ?>"></i>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="users.php"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= isActive('users.php') ?>">
                    <i class="fas fa-users-cog text-sm <?= activeIcon('users.php') ?>"></i>
                    <span class="font-medium text-sm">Kelola Pengguna</span>
                </a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] == 'user'): ?>
                <a href="create_ticket.php"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= isActive('create_ticket.php') ?>">
                    <i class="fas fa-plus-circle text-sm <?= activeIcon('create_ticket.php') ?>"></i>
                    <span class="font-semibold text-sm">Buat Request</span>
                </a>
            <?php endif; ?>

            <a href="riwayat.php"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= isActive('riwayat.php') ?>">
                <i class="fas fa-history text-sm <?= activeIcon('riwayat.php') ?>"></i>
                <span class="font-medium text-sm">Riwayat Tiket</span>
                <?php
                $badge_selesai = mysqli_fetch_assoc(mysqli_query(
                    $conn,
                    $_SESSION['role'] == 'admin'
                        ? "SELECT COUNT(*) as t FROM tickets WHERE status='selesai'"
                        : "SELECT COUNT(*) as t FROM tickets WHERE status='selesai' AND user_id='" . $_SESSION['user_id'] . "'"
                ))['t'];
                if ($badge_selesai > 0):
                ?>
                    <span class="ml-auto text-[9px] bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full font-black">
                        <?= $badge_selesai ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="guide.php"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= isActive('guide.php') ?>">
                <i class="fas fa-book-open text-sm <?= activeIcon('guide.php') ?>"></i>
                <span class="font-medium text-sm">Panduan Sistem</span>
            </a>

            <div class="pt-6">
                <p class="px-4 py-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">System</p>
                <a href="logout.php"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/10 hover:text-red-400 text-slate-400 transition-all duration-200">
                    <i class="fas fa-power-off text-sm"></i>
                    <span class="font-medium text-sm">Sign Out</span>
                </a>
            </div>
        </nav>

        <div class="p-6">
            <div class="bg-slate-800/50 rounded-2xl p-4 border border-slate-700/50 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold shrink-0">
                    <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-white truncate"><?= $_SESSION['username'] ?></p>
                    <span class="text-[9px] bg-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded-full font-black uppercase tracking-tighter">
                        <?= $_SESSION['role'] ?>
                    </span>
                </div>
            </div>
        </div>
    </aside>

    <div id="overlay" onclick="toggleMenu()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-[90] md:hidden"></div>

    <script>
        function toggleMenu() {
            const side = document.getElementById('sidebar');
            const over = document.getElementById('overlay');
            side.classList.toggle('-translate-x-full');
            over.classList.toggle('hidden');
        }
    </script>

    <main class="flex-1 w-full relative min-h-screen overflow-y-auto">
        <div class="p-6 md:p-10 max-w-7xl mx-auto">
<?php
include 'core/config.php';
if (isset($_SESSION['user_id'])) header("Location: index.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = input($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $res = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['divisi'] = $user['divisi'];
        header("Location: index.php");
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Login | SmartHelp</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .icon-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            box-shadow: 0 10px 25px rgba(168, 85, 247, 0.25);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        }

        .btn-gradient:hover {
            box-shadow: 0 10px 30px rgba(168, 85, 247, 0.4);
        }
    </style>
</head>

<body class="bg-[#fcfdfe] flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-[440px]">

        <div class="bg-white p-12 rounded-[2.8rem] shadow-[0_30px_70px_rgba(0,0,0,0.06)] border border-slate-50 transition-all duration-300">

            <div class="flex flex-col items-center text-center mb-12">
                <div class="w-16 h-16 icon-gradient rounded-[1.4rem] flex items-center justify-center border border-white/20 mb-4 transition-transform hover:scale-105">
                    <i class="fas fa-headset text-white text-3xl"></i>
                </div>
                <span class="text-3xl font-[800] text-slate-800 leading-none tracking-tight">SmartHelp</span>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-2">IT Solutions</span>
            </div>

            <div class="mb-10 text-center">
                <p class="text-slate-400 text-sm mt-2 font-medium"><b>Silakan masuk ke akun Anda.</b></p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-2xl mb-8 text-[11px] font-bold flex items-center gap-3 animate-pulse">
                    <i class="fas fa-circle-exclamation text-base"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6 text-left">
                <div class="space-y-2.5">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username / ID</label>
                    <div class="relative">
                        <i class="fas fa-user-circle absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-xl"></i>
                        <input type="text" name="username" placeholder="Masukkan username"
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 pl-14 pr-5 text-sm font-semibold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all" required>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-key absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-xl"></i>
                        <input type="password" name="password" id="passwordField" placeholder="••••••••"
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 pl-14 pr-12 text-sm font-semibold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 focus:bg-white outline-none transition-all" required>

                        <button type="button" onclick="togglePass()" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-indigo-500 transition-colors">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full btn-gradient text-white text-xs font-black uppercase tracking-[0.25em] py-5 rounded-2xl hover:-translate-y-1.5 transition-all duration-300 active:scale-95 flex items-center justify-center gap-3">
                        Masuk Sekarang <i class="fas fa-arrow-right-long text-sm"></i>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-slate-300 text-[11px] mt-16 font-bold tracking-widest uppercase italic opacity-70">
            Secure Access &bull; SmartHelp IT System
        </p>
    </div>

    <script>
        function togglePass() {
            const field = document.getElementById('passwordField');
            const icon = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>

</html>
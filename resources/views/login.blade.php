<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monev Susenas Seruti 2026</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        bps: {
                            orange: '#FF8C00',
                            yellow: '#FFD700',
                            dark: '#2D3436',
                        },
                        seorange: {
                            50: '#fff3ed',
                            100: '#ffe4d5',
                            200: '#ffc8ab',
                            300: '#ffa47a',
                            400: '#ff7540',
                            500: '#ff4c10',
                            600: '#ef3400',
                            700: '#c62300',
                            800: '#9d1c08',
                            900: '#7f1b0a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS for Glassmorphism & Blobs -->
    <style>
        body {
            background-color: #0f172a;
            min-height: 100vh;
        }

        .bg-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.25;
            animation: move 25s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(60px, 60px) scale(1.15); }
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        /* Webkit Autofill override */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #1e293b inset !important;
            -webkit-text-fill-color: #ffffff !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="font-sans flex items-center justify-center p-4 relative overflow-hidden select-none">

    <!-- Background Blobs -->
    <div class="bg-blobs">
        <div class="blob bg-bps-orange w-[500px] h-[500px] top-[-10%] left-[-10%]"></div>
        <div class="blob bg-bps-yellow w-[600px] h-[600px] bottom-[-20%] right-[-10%]" style="animation-delay: -5s;"></div>
        <div class="blob bg-orange-500 w-[400px] h-[400px] top-[30%] right-[10%]" style="animation-delay: -10s;"></div>
    </div>

    <!-- Login Container -->
    <div class="w-full max-w-md z-10 transition-all duration-500 transform hover:scale-[1.01]">
        <div class="glass-card rounded-3xl p-8 md:p-10 text-white">
            
            <!-- Branding Header -->
            <div class="flex flex-col items-center mb-10 text-center">
                <div class="w-16 h-16 bg-gradient-to-tr from-bps-orange to-bps-yellow rounded-2xl flex items-center justify-center shadow-lg shadow-bps-orange/20 mb-4 animate-pulse">
                    <i class="fa-solid fa-chart-line text-white text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight">Monev Susenas Seruti</h1>
                <p class="text-bps-yellow text-xs font-semibold uppercase tracking-widest mt-1">BPS Ogan Ilir</p>
                <div class="w-12 h-1 bg-gradient-to-r from-bps-orange to-bps-yellow rounded-full mt-4"></div>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5" id="loginForm">
                @csrf

                <!-- Username Input -->
                <div class="space-y-2">
                    <label for="username" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="username" id="username" required autofocus
                            class="w-full bg-slate-800/40 border border-slate-700/60 rounded-xl py-3.5 pl-12 pr-4 text-sm text-white focus:outline-none focus:border-bps-orange focus:ring-1 focus:ring-bps-orange transition-all duration-300"
                            placeholder="Masukkan username Anda">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label for="password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            class="w-full bg-slate-800/40 border border-slate-700/60 rounded-xl py-3.5 pl-12 pr-12 text-sm text-white focus:outline-none focus:border-bps-orange focus:ring-1 focus:ring-bps-orange transition-all duration-300"
                            placeholder="Masukkan password Anda">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-all">
                            <i class="fa-solid fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-800/40 text-bps-orange focus:ring-bps-orange mr-2">
                        <span>Ingat Saya</span>
                    </label>
                </div>

                <!-- Sign In Button -->
                <button type="submit" id="btnSubmit"
                    class="w-full bg-gradient-to-r from-bps-orange to-bps-orange/90 hover:from-bps-orange hover:to-orange-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-bps-orange/20 hover:shadow-orange-500/30 transition-all duration-300 transform active:scale-95 flex items-center justify-center gap-2">
                    <span>Masuk</span>
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Password Visibility Toggle
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('password-eye');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Handle Form Loading State
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    <span>Memproses...</span>
                </div>
            `;
        });

        // Show SweetAlert alerts if they exist in the session or errors bag
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#FF8C00',
                background: '#1e293b',
                color: '#fff',
                confirmButtonText: 'Coba Lagi'
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#FF8C00',
                background: '#1e293b',
                color: '#fff',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    </script>
</body>
</html>

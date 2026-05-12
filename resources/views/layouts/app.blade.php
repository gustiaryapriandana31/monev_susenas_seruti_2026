<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monev SE2026 - Dashboard</title>

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

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }

        /* Clean & Subtle Background Blobs */
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
            filter: blur(100px); /* Increased blur for softer look */
            opacity: 0.15; /* Much lower opacity for cleanliness */
            animation: move 25s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(50px, 50px) scale(1.1); }
        }

        /* Clean Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .glass-dark {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(255, 76, 16, 0.9) 0%, rgba(255, 140, 0, 0.9) 100%);
            color: white !important;
            box-shadow: 0 4px 15px rgba(255, 76, 16, 0.2);
            border-radius: 0.5rem;
        }

        /* Clean DataTables UI */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(203, 213, 225, 0.6);
            border-radius: 0.5rem;
            padding: 0.4rem 0.8rem;
            outline: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            background: #fff;
            border-color: #FF8C00;
            box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.1);
        }

        .dataTables_wrapper {
            width: 100%;
            max-width: 100%;
        }

        .dataTables_wrapper .dataTables_processing {
            position: absolute;
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 9999px; /* Pill shape */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            padding: 0.5rem 1rem !important;
            width: auto !important;
            height: auto !important;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%);
            margin: 0 !important;
        }

        /* Hide DataTables native blue dots loader */
        .dataTables_wrapper .dataTables_processing > div:not(.custom-loader) {
            display: none !important;
        }

        .dataTables_scroll {
            width: 100%;
        }

        .dataTables_scrollHead,
        .dataTables_scrollBody {
            -webkit-overflow-scrolling: touch;
        }

        .dataTables_scrollBody {
            border-bottom: none !important;
        }

        table.dataTable.nowrap th,
        table.dataTable.nowrap td {
            white-space: nowrap;
        }

        /* Clean Table Style */
        table.dataTable {
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            background: transparent !important;
            width: 100% !important;
        }

        table.dataTable thead th {
            background: rgba(248, 250, 252, 0.7) !important;
            border-bottom: 2px solid rgba(226, 232, 240, 0.8) !important;
            padding: 12px 16px !important;
            color: #475569 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            border-top: none !important;
        }

        table.dataTable tbody tr {
            background: rgba(255, 255, 255, 0.4) !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.6) !important;
            transition: background 0.2s ease;
        }

        table.dataTable tbody tr:hover {
            background: rgba(241, 245, 249, 0.8) !important;
        }

        table.dataTable tbody td {
            border-top: none !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.6) !important;
            padding: 12px 16px !important;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #FF8C00 !important;
            color: white !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 0.4rem 0.8rem !important;
            box-shadow: 0 2px 5px rgba(255, 140, 0, 0.2);
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.2);
        }
    </style>
    @stack('styles')
</head>
<body class="font-jakarta text-gray-700 overflow-x-hidden">
    <!-- Decorative Background Blobs -->
    <div class="bg-blobs">
        <div class="blob bg-bps-orange w-[500px] h-[500px] top-[-10%] left-[-10%]"></div>
        <div class="blob bg-bps-yellow w-[600px] h-[600px] bottom-[-20%] right-[-10%]" style="animation-delay: -5s;"></div>
        <div class="blob bg-orange-300 w-[400px] h-[400px] top-[30%] right-[20%]" style="animation-delay: -10s;"></div>
        <div class="blob bg-yellow-200 w-[300px] h-[300px] bottom-[20%] left-[20%]" style="animation-delay: -15s;"></div>
    </div>
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed left-0 top-0 h-full w-72 glass-dark z-50 transition-all duration-300 transform -translate-x-full lg:translate-x-0">
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="p-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-tr from-bps-orange to-bps-yellow rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-chart-line text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-white font-bold text-xl tracking-tight leading-tight">Monev SE</h1>
                            <p class="text-bps-yellow text-[10px] font-semibold uppercase tracking-widest">Ogan Ilir</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                @php $activeTab = session('active_tab', 'lapangan'); @endphp
                <nav class="flex-1 px-4 space-y-2 py-4">
                    <div class="text-gray-400 text-xs font-bold uppercase tracking-widest px-4 mb-4">Main Menu</div>
                    
                    <a href="#" data-section="lapangan" class="sidebar-link {{ $activeTab === 'lapangan' ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-users w-6"></i>
                        <span class="font-medium">Petugas Lapangan</span>
                    </a>
                    
                    <a href="#" data-section="entry" class="sidebar-link {{ $activeTab === 'entry' ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-user-pen w-6"></i>
                        <span class="font-medium">Petugas Entry Data</span>
                    </a>
                    
                    <a href="#" data-section="dssls" class="sidebar-link {{ $activeTab === 'dssls' ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-map-location-dot w-6"></i>
                        <span class="font-medium">Data DSSLS</span>
                    </a>
                    
                    <a href="#" data-section="dsrt" class="sidebar-link {{ $activeTab === 'dsrt' ? 'active' : '' }} flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-300 hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-file-invoice w-6"></i>
                        <span class="font-medium">Data DSRT</span>
                    </a>
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-6">
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-bps-orange/20 flex items-center justify-center">
                                <i class="fa-solid fa-circle-info text-bps-orange"></i>
                            </div>
                            <div class="text-xs">
                                <p class="text-gray-400">Versi Sistem</p>
                                <p class="text-white font-semibold">1.0.0-Beta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 min-w-0 overflow-hidden lg:ml-72 transition-all duration-300">
            <!-- Topbar -->
            <header class="h-20 glass sticky top-0 z-40 px-8 flex items-center justify-between mx-4 mt-4 rounded-2xl">
                <div class="flex items-center space-x-4">
                    <button id="sidebar-toggle" class="lg:hidden text-bps-dark text-2xl focus:outline-none">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                    <div>
                        <h2 id="current-section-title" class="text-bps-dark font-bold text-xl">@php
                            echo ['lapangan'=>'Petugas Lapangan','entry'=>'Petugas Entry Data','dssls'=>'Data DSSLS','dsrt'=>'Data DSRT'][$activeTab] ?? 'Dashboard';
                        @endphp</h2>
                        <p class="text-gray-500 text-xs">Monitoring Sensus Ekonomi 2026</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-6">
                    <div class="hidden md:flex items-center space-x-2 text-sm font-medium text-gray-600 bg-white/50 px-4 py-2 rounded-full border border-white/50">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span>Sistem Online</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-bps-orange to-bps-yellow flex items-center justify-center text-white shadow-lg cursor-pointer">
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Setup CSRF token for AJAX globally
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Sidebar Toggle
        $('#sidebar-toggle').on('click', function() {
            $('#sidebar').toggleClass('-translate-x-full');
        });

        // Navigation Logic — lightweight, no fake clicks
        $('.sidebar-link').on('click', function(e) {
            e.preventDefault();
            var section = $(this).data('section');

            // Update sidebar active state
            $('.sidebar-link').removeClass('active');
            $(this).addClass('active');
            $('#current-section-title').text($(this).find('span').text());

            // Show/Hide Sections
            $('.dashboard-section').addClass('hidden');
            $('#section-' + section).removeClass('hidden');

            // Initialize table for this section (handles columns.adjust internally)
            $(document).trigger('tabChanged', [section]);

            // Close sidebar on mobile
            if ($(window).width() < 1024) {
                $('#sidebar').addClass('-translate-x-full');
            }
        });

        // Flash Messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#ff4c10',
                background: 'rgba(255,255,255,0.9)',
                backdrop: 'rgba(0,0,0,0.4)'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ff4c10'
            });
        @endif
        
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Error!',
                html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#ff4c10'
            });
        @endif
    </script>
    
    @stack('scripts')
</body>
</html>

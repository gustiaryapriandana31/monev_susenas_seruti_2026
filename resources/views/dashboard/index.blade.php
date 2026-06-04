@extends('layouts.app')

@php
    $activeTab = session('active_tab', 'dashboard');
    $isSuperAdmin  = Auth::user()->isSuperAdmin();
    $isAdminIpds = Auth::user()->isAdminIpds();
    $isAdminSosial = Auth::user()->isAdminSosial();
@endphp

@section('content')
    <div class="space-y-8">

        <!-- =========================================================
         Section: Dashboard Utama
    ========================================================== -->
    <div id="section-dashboard" class="dashboard-section {{ $activeTab !== 'dashboard' ? 'hidden' : '' }} space-y-6">
        
        <!-- Interactive Location Filters -->
        <div class="glass p-6 rounded-2xl flex flex-wrap gap-4 items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-bps-orange/10 flex items-center justify-center text-bps-orange">
                    <i class="fa-solid fa-filter text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-bps-dark">Filter Wilayah</h4>
                    <p class="text-xs text-gray-500">Batasi statistik berdasarkan kecamatan dan desa</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                <div class="w-full md:w-48">
                    <select id="filter-kecamatan" class="w-full rounded-lg border border-gray-200 p-2.5 text-xs focus:ring-bps-orange focus:outline-none">
                        <option value="">Semua Kecamatan</option>
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <select id="filter-desa" class="w-full rounded-lg border border-gray-200 p-2.5 text-xs focus:ring-bps-orange focus:outline-none" disabled>
                        <option value="">Semua Desa/Kelurahan</option>
                    </select>
                </div>
                <button id="btn-reset-filter" class="w-full md:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrows-rotate"></i> Reset
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass p-6 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Total PPL</p>
                    <h4 class="text-3xl font-black text-bps-dark" id="summary-ppl">...</h4>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-tr from-bps-orange to-bps-yellow flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
            <div class="glass p-6 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Total PML</p>
                    <h4 class="text-3xl font-black text-bps-dark" id="summary-pml">...</h4>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-tr from-bps-yellow to-orange-400 flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-user-tie text-2xl"></i>
                </div>
            </div>
            <div class="glass p-6 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Total Entry</p>
                    <h4 class="text-3xl font-black text-bps-dark" id="summary-entry">...</h4>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-tr from-orange-500 to-red-500 flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-user-pen text-2xl"></i>
                </div>
            </div>
            <div class="glass p-6 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Total Petugas</p>
                    <h4 class="text-3xl font-black text-bps-dark" id="summary-total-petugas">...</h4>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gray-800 flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-users-gear text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Overall Progress Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- DSSLS Overall Progress Card -->
            <div class="glass p-6 rounded-2xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-500">Progres Keseluruhan DSSLS</h4>
                        <span class="text-xs font-black text-white px-2 py-0.5 rounded-full bg-bps-orange" id="dssls-progress-badge">0%</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">SLS yang selesai seluruh tahapan (Ceklis Lapangan, Sosial, dan IPDS bernilai YA)</p>
                </div>
                <div>
                    <div class="w-full bg-gray-100 rounded-full h-3 mb-2 overflow-hidden">
                        <div id="dssls-progress-bar" class="bg-gradient-to-r from-bps-orange to-bps-yellow h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Selesai: <strong id="dssls-completed-count">0</strong> SLS</span>
                        <span>Target: <strong id="dssls-total-count">0</strong> SLS</span>
                    </div>
                </div>
            </div>

            <!-- DSRT Overall Progress Card -->
            <div class="glass p-6 rounded-2xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-500">Progres Keseluruhan DSRT</h4>
                        <span class="text-xs font-black text-white px-2 py-0.5 rounded-full bg-bps-orange" id="dsrt-progress-badge">0%</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Keluarga/Ruta yang selesai seluruh tahapan (Ceklis Lapangan, Sosial, IPDS, dan Pemeriksaan bernilai YA)</p>
                </div>
                <div>
                    <div class="w-full bg-gray-100 rounded-full h-3 mb-2 overflow-hidden">
                        <div id="dsrt-progress-bar" class="bg-gradient-to-r from-bps-orange to-bps-yellow h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Selesai: <strong id="dsrt-completed-count">0</strong> Keluarga/Ruta</span>
                        <span>Target: <strong id="dsrt-total-count">0</strong> Keluarga/Ruta</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Jumlah Ceklis breakdown charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- DSSLS Progress Chart -->
            <div class="glass p-6 rounded-2xl shadow-sm flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-bps-dark">Jumlah Ceklis DSSLS</h3>
                        <p class="text-gray-500 text-xs">Total Target: <span id="chart-dssls-total" class="font-bold text-bps-dark">...</span> SLS</p>
                    </div>
                </div>
                <div class="relative flex-1 w-full flex items-center justify-center min-h-[300px]">
                    <canvas id="chart-dssls"></canvas>
                </div>
            </div>

            <!-- DSRT Progress Chart -->
            <div class="glass p-6 rounded-2xl shadow-sm flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-bps-dark">Jumlah Ceklis DSRT</h3>
                        <p class="text-gray-500 text-xs">Total Target: <span id="chart-dsrt-total" class="font-bold text-bps-dark">...</span> Keluarga/Ruta</p>
                    </div>
                </div>
                <div class="relative flex-1 w-full flex items-center justify-center min-h-[300px]">
                    <canvas id="chart-dsrt"></canvas>
                </div>
            </div>
        </div>

        <!-- Rekap Petugas Entry Section -->
        <div class="glass p-6 rounded-2xl shadow-sm flex flex-col space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-bps-dark">Rekap Penugasan Petugas Entry</h3>
                    <p class="text-gray-500 text-xs">Jumlah beban tugas entry data per petugas</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    {{-- Export Button --}}
                    <button type="button" onclick="exportRekapPetugas()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-file-excel"></i> Export Rekap
                    </button>
                    {{-- Search bar --}}
                    <div class="relative w-full md:w-64">
                        <input type="text" id="search-rekap" placeholder="Cari nama atau kode petugas..." class="w-full rounded-lg border border-gray-200 p-2.5 pl-9 text-xs focus:ring-bps-orange focus:outline-none">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-[11px] text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-200/80">
                            <th class="py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center w-12">No</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Nama Petugas</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Kode Petugas</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">ENTRY PEMUTAKHIRAN</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">ENTRY SUSENAS</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">ENTRY SERUTI</th>
                            <th class="py-3 px-4 text-[10px] font-bold text-bps-orange uppercase tracking-wider text-center font-bold">TOTAL TUGAS</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-rekap" class="divide-y divide-gray-100">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-gray-400 font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-4 h-4 border-2 border-bps-orange border-t-transparent rounded-full animate-spin"></div>
                                    Memuat rekap petugas...
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

        <!-- =========================================================
             Section: Petugas Lapangan
        ========================================================== -->
        <div id="section-lapangan" class="dashboard-section {{ $activeTab !== 'lapangan' ? 'hidden' : '' }} space-y-6">
            <div class="glass p-6 rounded-md">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-bps-dark">Petugas Lapangan</h3>
                        <p class="text-gray-500 text-sm">Kelola data petugas yang bertugas di lapangan</p>
                    </div>
                    @if ($isSuperAdmin || $isAdminSosial)
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" onclick="openAddLapanganModal()"
                                class="bg-bps-orange hover:bg-seorange-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                                <i class="fa-solid fa-user-plus"></i> Tambah Petugas
                            </button>
                            <form action="{{ route('petugas_lapangan.import') }}" method="POST"
                                enctype="multipart/form-data"
                                class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-inner">
                                @csrf
                                <input type="file" name="file_excel"
                                    class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-bps-orange file:text-white hover:file:bg-seorange-600 cursor-pointer"
                                    accept=".xlsx,.xls,.csv" required>
                                <button type="submit"
                                    class="bg-bps-dark hover:bg-black text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i>Import
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic"><i class="fa-solid fa-eye mr-1"></i>Mode Lihat
                            Saja</span>
                    @endif
                </div>
            </div>

            {{-- Tombol hapus/reset di-inject ke toolbar DataTables --}}
            @if ($isSuperAdmin || $isAdminSosial)
            <div id="dt-actions-lapangan" class="hidden gap-1.5">
                <button type="button" onclick="deleteSelected('lapangan')"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-trash-can mr-1.5"></i>Hapus Terpilih
                </button>
                <button type="button" onclick="deleteAll('lapangan')"
                    class="bg-red-800 hover:bg-red-900 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-dumpster mr-1.5"></i>Reset
                </button>
            </div>
            @endif

            <div class="glass p-6 rounded-md overflow-x-auto">
                <table id="dt-lapangan" class="display nowrap w-full" style="width:100%">
                    <thead>
                        <tr>
                            @if ($isSuperAdmin || $isAdminSosial)
                                <th class="w-10 text-center"><input type="checkbox" id="selectAllLapangan"
                                        class="w-4 h-4 rounded-md border-gray-300"></th>
                            @endif
                            <th>Kode</th>
                            <th>Provinsi</th>
                            <th>Kabupaten</th>
                            <th>Nama Petugas</th>
                            <th>No HP</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-lapangan">
                        <tr>
                            <td colspan="{{ $isSuperAdmin || $isAdminSosial ? 8 : 7 }}" class="text-center py-8">
                                <div class="flex items-center justify-center gap-3">
                                    <div
                                        class="w-5 h-5 border-2 border-bps-orange border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <div class="text-xs text-gray-400">Memuat data petugas lapangan...</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================
             Section: Petugas Entry
        ========================================================== -->
        <div id="section-entry" class="dashboard-section {{ $activeTab !== 'entry' ? 'hidden' : '' }} space-y-6">
            <div class="glass p-6 rounded-md">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-bps-dark">Petugas Entry Data</h3>
                        <p class="text-gray-500 text-sm">Kelola data petugas pengolahan dan entri data</p>
                    </div>
                    @if ($isSuperAdmin || $isAdminIpds)
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" onclick="openAddEntryModal()"
                                class="bg-bps-orange hover:bg-seorange-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                                <i class="fa-solid fa-user-plus"></i> Tambah Petugas
                            </button>
                            <form action="{{ route('petugas_entry.import') }}" method="POST" enctype="multipart/form-data"
                                class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-inner">
                                @csrf
                                <input type="file" name="file_excel"
                                    class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-bps-orange file:text-white hover:file:bg-seorange-600 cursor-pointer"
                                    accept=".xlsx,.xls,.csv" required>
                                <button type="submit"
                                    class="bg-bps-dark hover:bg-black text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i>Import
                                </button>
                            </form>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic"><i class="fa-solid fa-eye mr-1"></i>Mode Lihat
                            Saja</span>
                    @endif
                </div>
            </div>

            {{-- Tombol hapus/reset di-inject ke toolbar DataTables --}}
            @if ($isSuperAdmin || $isAdminIpds)
            <div id="dt-actions-entry" class="hidden gap-1.5">
                <button type="button" onclick="deleteSelected('entry')"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-trash-can mr-1.5"></i>Hapus Terpilih
                </button>
                <button type="button" onclick="deleteAll('entry')"
                    class="bg-red-800 hover:bg-red-900 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-dumpster mr-1.5"></i>Reset
                </button>
            </div>
            @endif

            <div class="glass p-6 rounded-md overflow-x-auto">
                <table id="dt-entry" class="display nowrap w-full" style="width:100%">
                    <thead>
                        <tr>
                            @if ($isSuperAdmin || $isAdminIpds)
                                <th class="w-10 text-center"><input type="checkbox" id="selectAllEntry"
                                        class="w-4 h-4 rounded-md border-gray-300"></th>
                            @endif
                            <th>Kode</th>
                            <th>Provinsi</th>
                            <th>Kabupaten</th>
                            <th>Nama Petugas</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-entry">
                        <tr>
                            <td colspan="{{ $isSuperAdmin || $isAdminIpds ? 8 : 7 }}" class="text-center py-8">
                                <div class="flex items-center justify-center gap-3">
                                    <div
                                        class="w-5 h-5 border-2 border-bps-orange border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <div class="text-xs text-gray-400">Memuat data petugas entry...</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================
             Section: Data DSSLS
        ========================================================== -->
        <div id="section-dssls" class="dashboard-section {{ $activeTab !== 'dssls' ? 'hidden' : '' }} space-y-6">
            <div class="glass p-6 rounded-md">
                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
                    {{-- Kiri: Judul --}}
                    <div>
                        <h3 class="text-2xl font-bold text-bps-dark">Data DSSLS</h3>
                        <p class="text-gray-500 text-sm">Monitoring progres pemuktahiran DSSLS</p>
                    </div>

                    {{-- Kanan: Tombol aksi --}}
                    @if ($isAdminIpds)
                        <span class="text-xs text-gray-400 italic">
                            <i class="fa-solid fa-eye mr-1"></i>Mode Lihat Saja
                        </span>
                    @else
                        <div class="flex flex-wrap items-center gap-3">

                            {{-- Import --}}
                            <form action="{{ route('data_dssls.import') }}" method="POST" enctype="multipart/form-data"
                                class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-inner">
                                @csrf
                                <input type="file" name="file_excel"
                                    class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-bps-orange file:text-white hover:file:bg-seorange-600 cursor-pointer"
                                    accept=".xlsx,.xls,.csv" required>
                                <button type="submit"
                                    class="bg-bps-dark hover:bg-black text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i>Import
                                </button>
                            </form>

                            {{-- Export --}}
                            <a href="{{ route('data_dssls.export') }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                                <i class="fa-solid fa-file-excel mr-2"></i>Export DSSLS Pemutakhiran
                            </a>

                        </div>
                    @endif
                </div>
            </div>


            {{-- Tombol hapus/reset di-inject ke toolbar DataTables --}}
            @if (!$isAdminIpds)
            <div id="dt-actions-dssls" class="hidden gap-1.5">
                <button type="button" onclick="deleteSelected('dssls')"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-trash-can mr-1.5"></i>Hapus Terpilih
                </button>
                <button type="button" onclick="deleteAll('dssls')"
                    class="bg-red-800 hover:bg-red-900 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-dumpster mr-1.5"></i>Reset
                </button>
            </div>
            @endif

            <div class="glass p-6 rounded-md overflow-x-auto">
                <table id="dt-dssls" class="display nowrap w-full" style="width:100%">
                    <thead>
                        <tr>
                            {{-- Checkbox & Action: hanya non-adminipds --}}
                            @if (!$isAdminIpds)
                                <th class="w-10 text-center bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800"><input type="checkbox" id="selectAllDssls"
                                        class="w-4 h-4 rounded-md border-gray-300"></th>
                                <th class="bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800">Action</th>
                            @endif
                            <th class="bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800">Wilayah</th>
                            <th class="bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800">SLS</th>
                            
                            {{-- Group 1: LAPANGAN s/d RUTA HASIL UPDATING (soft orange) --}}
                            <th class="text-center bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800 border-l border-l-orange-200 dark:border-l-orange-800">Lapangan</th>
                            @if ($isSuperAdmin || $isAdminSosial)
                                <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">PPL</th>
                                <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">PML</th>
                            @endif
                            <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">Keluarga Awal</th>
                            <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">Keluarga Hasil Updating</th>
                            <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">Ruta Hasil Updating</th>
                            
                            {{-- Group 2: SOSIAL (soft blue) --}}
                            <th class="text-center bg-blue-100/70 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 border-b border-blue-200 dark:border-blue-800 border-l border-l-blue-200 dark:border-l-blue-800">Sosial</th>
                            
                            {{-- Group 3: IPDS s/d ENTRY (soft teal) --}}
                            <th class="text-center bg-teal-100/70 dark:bg-teal-950/20 text-teal-700 dark:text-teal-300 border-b border-teal-200 dark:border-teal-800 border-l border-l-teal-200 dark:border-l-teal-800">IPDS</th>
                            @if ($isSuperAdmin || $isAdminIpds)
                                <th class="bg-teal-100/70 dark:bg-teal-950/20 text-teal-700 dark:text-teal-300 border-b border-teal-200 dark:border-teal-800">Entry</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tbody-dssls">
                        <tr>
                            <td colspan="13" class="text-center py-8">
                                <div class="flex items-center justify-center gap-3">
                                    <div
                                        class="w-5 h-5 border-2 border-bps-orange border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <div class="text-xs text-gray-400">Memuat data pemutakhiran DSSLS...</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================
             Section: Data DSRT
        ========================================================== -->
        <div id="section-dsrt" class="dashboard-section {{ $activeTab !== 'dsrt' ? 'hidden' : '' }} space-y-6">
            <div class="glass p-6 rounded-md">
                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-bps-dark">Data DSRT</h3>
                        <p class="text-gray-500 text-sm">Monitoring progres pemuktahiran DSRT</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        @if ($isSuperAdmin)
                            <form action="{{ route('data_dsrt.import') }}" method="POST" enctype="multipart/form-data"
                                class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-inner">
                                @csrf
                                <input type="file" name="file_excel"
                                    class="text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-bps-orange file:text-white hover:file:bg-seorange-600 cursor-pointer"
                                    accept=".xlsx,.xls,.csv" required>
                                <button type="submit"
                                    class="bg-bps-dark hover:bg-black text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i>Import
                                </button>
                            </form>
                        @endif

                        {{-- Export Data Dropdown: superadmin, adminipds, adminsosial --}}
                        @if ($isSuperAdmin || $isAdminIpds || $isAdminSosial)
                            <div class="relative inline-block text-left" id="dsrt-export-wrapper">
                                <button type="button" id="dsrt-export-btn" onclick="toggleDsrtExportDropdown(event)"
                                    class="inline-flex items-center gap-2 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md focus:outline-none"
                                    style="background: linear-gradient(135deg, #0d9488, #0f766e); box-shadow: 0 4px 14px rgba(13,148,136,0.35);">
                                    <i class="fa-solid fa-file-export"></i>
                                    Export Data
                                    <i class="fa-solid fa-chevron-down text-[10px] ml-0.5 transition-transform duration-200" id="dsrt-export-chevron"></i>
                                </button>

                                {{-- Dropdown Menu: fixed positioning agar lepas dari stacking context backdrop-filter --}}
                                <div id="dsrt-export-menu"
                                    class="hidden rounded-2xl bg-white border border-stone-100 overflow-hidden"
                                    style="position: fixed; min-width: 240px; z-index: 99999; box-shadow: 0 12px 32px rgba(0,0,0,0.14), 0 2px 8px rgba(0,0,0,0.07);">

                                    {{-- Header kecil --}}
                                    <div class="px-4 py-2.5 bg-gradient-to-r from-stone-50 to-stone-100 border-b border-stone-100">
                                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Pilih Format Export</span>
                                    </div>

                                    <div class="py-1.5 px-1.5 flex flex-col gap-0.5">
                                        {{-- Export IPDS: superadmin & adminipds --}}
                                        @if ($isSuperAdmin || $isAdminIpds)
                                            <a href="{{ route('data_dsrt.export_ipds') }}"
                                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-blue-700 hover:bg-blue-50 transition-all group">
                                                <span class="w-7 h-7 rounded-lg bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center flex-shrink-0 transition-colors">
                                                    <i class="fa-solid fa-file-excel text-blue-600 text-[11px]"></i>
                                                </span>
                                                Export IPDS
                                            </a>
                                        @endif

                                        {{-- Divider antara IPDS & Sosial jika superadmin --}}
                                        @if ($isSuperAdmin)
                                            <div class="border-t border-stone-100 my-1 mx-1"></div>
                                        @endif

                                        {{-- Export Sosial: superadmin & adminsosial --}}
                                        @if ($isSuperAdmin || $isAdminSosial)
                                            <a href="{{ route('data_dsrt.export_sosial') }}"
                                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-teal-700 hover:bg-teal-50 transition-all group">
                                                <span class="w-7 h-7 rounded-lg bg-teal-100 group-hover:bg-teal-200 flex items-center justify-center flex-shrink-0 transition-colors">
                                                    <i class="fa-solid fa-file-excel text-teal-600 text-[11px]"></i>
                                                </span>
                                                Export Sosial Penerimaan oleh Kab
                                            </a>
                                            <a href="{{ route('data_dsrt.export_sosial_kab') }}"
                                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-teal-700 hover:bg-teal-50 transition-all group">
                                                <span class="w-7 h-7 rounded-lg bg-teal-100 group-hover:bg-teal-200 flex items-center justify-center flex-shrink-0 transition-colors">
                                                    <i class="fa-solid fa-file-excel text-teal-600 text-[11px]"></i>
                                                </span>
                                                Export Sosial Pengiriman ke Kab
                                            </a>
                                            <a href="{{ route('data_dsrt.export_lapangan') }}"
                                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-teal-700 hover:bg-teal-50 transition-all group">
                                                <span class="w-7 h-7 rounded-lg bg-teal-100 group-hover:bg-teal-200 flex items-center justify-center flex-shrink-0 transition-colors">
                                                    <i class="fa-solid fa-map-location-dot text-teal-600 text-[11px]"></i>
                                                </span>
                                                Export Lapangan
                                            </a>
                                            <a href="{{ route('data_dsrt.export_pemeriksaan') }}"
                                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-teal-700 hover:bg-teal-50 transition-all group">
                                                <span class="w-7 h-7 rounded-lg bg-teal-100 group-hover:bg-teal-200 flex items-center justify-center flex-shrink-0 transition-colors">
                                                    <i class="fa-solid fa-clipboard-check text-teal-600 text-[11px]"></i>
                                                </span>
                                                Export Pemeriksaan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            {{-- Tombol hapus/reset di-inject ke toolbar DataTables --}}
            @if ($isSuperAdmin)
            <div id="dt-actions-dsrt" class="hidden gap-1.5">
                <button type="button" onclick="deleteSelected('dsrt')"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-trash-can mr-1.5"></i>Hapus Terpilih
                </button>
                <button type="button" onclick="deleteAll('dsrt')"
                    class="bg-red-800 hover:bg-red-900 text-white px-3 py-1.5 rounded-md text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-dumpster mr-1.5"></i>Reset
                </button>
            </div>
            @endif

            <div class="glass p-6 rounded-md overflow-x-auto">
                <table id="dt-dsrt" class="display nowrap w-full" style="width:100%">
                    <thead>
                        <tr>
                            {{-- Checkbox: hanya superadmin --}}
                            @if ($isSuperAdmin)
                                <th class="w-10 text-center bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800"><input type="checkbox" id="selectAllDsrt"
                                        class="w-4 h-4 rounded-md border-gray-300"></th>
                            @endif
                            <th class="bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800">Action</th>
                            <th class="bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800">Wilayah</th>
                            <th class="bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800">NBS/NKS</th>
                            <th class="bg-stone-50/80 dark:bg-stone-900/50 text-stone-700 dark:text-stone-300 border-b border-stone-200 dark:border-stone-800">KRT</th>

                            {{-- Group 1: LAPANGAN s/d PML (soft orange) --}}
                            <th class="text-center bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800 border-l border-l-orange-200 dark:border-l-orange-800">Lapangan</th>
                            @if ($isSuperAdmin || $isAdminSosial)
                                <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">R203 KOR</th>
                                <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">R203 KP</th>
                                <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">PPL</th>
                                <th class="bg-orange-100/70 dark:bg-orange-950/20 text-orange-700 dark:text-orange-300 border-b border-orange-200 dark:border-orange-800">PML</th>
                            @endif

                            {{-- Group 2: SOSIAL s/d CATATAN KP (soft blue) --}}
                            <th class="text-center bg-blue-100/70 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 border-b border-blue-200 dark:border-blue-800 border-l border-l-blue-200 dark:border-l-blue-800">Sosial</th>
                            @if ($isSuperAdmin || $isAdminSosial)
                                <th class="bg-blue-100/70 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 border-b border-blue-200 dark:border-blue-800">Catatan KOR</th>
                                <th class="bg-blue-100/70 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 border-b border-blue-200 dark:border-blue-800">Catatan KP</th>
                            @endif

                            {{-- Group 3: PEMERIKSAAN s/d R305 (soft teal) --}}
                            <th class="text-center bg-teal-100/70 dark:bg-teal-950/20 text-teal-700 dark:text-teal-300 border-b border-teal-200 dark:border-teal-800 border-l border-l-teal-200 dark:border-l-teal-800">Pemeriksaan</th>
                            @if ($isSuperAdmin || $isAdminSosial)
                                <th class="bg-teal-100/70 dark:bg-teal-950/20 text-teal-700 dark:text-teal-300 border-b border-teal-200 dark:border-teal-800">R301 Jml ART</th>
                                <th class="bg-teal-100/70 dark:bg-teal-950/20 text-teal-700 dark:text-teal-300 border-b border-teal-200 dark:border-teal-800">R304 (VSEN26-KP)</th>
                                <th class="bg-teal-100/70 dark:bg-teal-950/20 text-teal-700 dark:text-teal-300 border-b border-teal-200 dark:border-teal-800">R305 (VSEN26-KP)</th>
                            @endif

                            {{-- Group 4: IPDS s/d SERUTI (soft purple) --}}
                            @if ($isSuperAdmin || $isAdminIpds)
                                <th class="text-center bg-purple-100/70 dark:bg-purple-950/20 text-purple-700 dark:text-purple-300 border-b border-purple-200 dark:border-purple-800 border-l border-l-purple-200 dark:border-l-purple-800">IPDS</th>
                                <th class="bg-purple-100/70 dark:bg-purple-950/20 text-purple-700 dark:text-purple-300 border-b border-purple-200 dark:border-purple-800">Susenas</th>
                                <th class="bg-purple-100/70 dark:bg-purple-950/20 text-purple-700 dark:text-purple-300 border-b border-purple-200 dark:border-purple-800">Seruti</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tbody-dsrt">
                        <tr>
                            <td colspan="20" class="text-center py-8">
                                <div class="flex items-center justify-center gap-3">
                                    <div
                                        class="w-5 h-5 border-2 border-bps-orange border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <div class="text-xs text-gray-400">Memuat data pemutakhiran DSRT...</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- =========================================================
        Modals
    ========================================================== -->

    {{-- Modal Tambah Petugas Lapangan --}}
    @if ($isSuperAdmin || $isAdminSosial)
        <div id="modal-add-lapangan" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('modal-add-lapangan')"></div>
            <div class="relative bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl scale-in border border-gray-100">
                <div class="px-8 py-5 bg-gradient-to-r from-bps-orange/5 to-bps-orange/10 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-extrabold text-bps-dark">Tambah Petugas Lapangan</h3>
                        <p class="text-gray-400 text-[10px] mt-0.5 uppercase tracking-widest font-bold">Input Informasi Petugas Baru</p>
                    </div>
                    <button type="button" onclick="closeModal('modal-add-lapangan')"
                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all border border-gray-100 shadow-sm">
                        <i class="fa-solid fa-times text-sm"></i>
                    </button>
                </div>
                <form id="form-add-lapangan" action="{{ route('petugas_lapangan.store') }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">Kode Petugas</label>
                            <div class="relative">
                                <input type="text" name="kode_petugas" required placeholder="Contoh: 16109999"
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-4 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm">
                                <i class="fa-solid fa-id-card absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            </div>
                            <span id="error-kode_petugas" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">Nama Petugas</label>
                            <div class="relative">
                                <input type="text" name="nama_petugas" required placeholder="Nama lengkap petugas..."
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-4 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm">
                                <i class="fa-solid fa-user absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            </div>
                            <span id="error-nama_petugas" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">No HP</label>
                            <div class="relative">
                                <input type="text" name="no_hp" required placeholder="Contoh: 0853..."
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-4 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm">
                                <i class="fa-solid fa-phone absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            </div>
                            <span id="error-no_hp" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">Jabatan</label>
                            <div class="relative">
                                <select name="jabatan" required
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-10 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm appearance-none cursor-pointer">
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Pencacah (PPL)">Pencacah (PPL)</option>
                                    <option value="Pengawas (PML)">Pengawas (PML)</option>
                                </select>
                                <i class="fa-solid fa-briefcase absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                                <i class="fa-solid fa-chevron-down absolute right-3.5 top-3 text-gray-400 text-[10px] pointer-events-none"></i>
                            </div>
                            <span id="error-jabatan" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">Status</label>
                        <div class="relative">
                            <select name="status" required
                                class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-10 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm appearance-none cursor-pointer">
                                <option value="">-- Pilih Status --</option>
                                <option value="Mitra">Mitra</option>
                                <option value="Staf Kabupaten">Staf Kabupaten</option>
                            </select>
                            <i class="fa-solid fa-user-shield absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-3 text-gray-400 text-[10px] pointer-events-none"></i>
                        </div>
                        <span id="error-status" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 bg-gray-50/50 px-8 py-4 -mx-8 -mb-8">
                        <button type="button" onclick="closeModal('modal-add-lapangan')"
                            class="px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 font-bold text-xs hover:bg-gray-100 hover:text-gray-800 active:scale-95 transition-all shadow-sm">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-bps-orange to-orange-500 text-white font-extrabold text-xs shadow-md shadow-bps-orange/20 hover:shadow-lg hover:shadow-bps-orange/30 hover:brightness-105 active:scale-95 transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-sm"></i> Simpan Petugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Tambah Petugas Entry --}}
    @if ($isSuperAdmin || $isAdminIpds)
        <div id="modal-add-entry" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('modal-add-entry')"></div>
            <div class="relative bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl scale-in border border-gray-100">
                <div class="px-8 py-5 bg-gradient-to-r from-bps-orange/5 to-bps-orange/10 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-extrabold text-bps-dark">Tambah Petugas Entry Data</h3>
                        <p class="text-gray-400 text-[10px] mt-0.5 uppercase tracking-widest font-bold">Input Informasi Petugas Baru</p>
                    </div>
                    <button type="button" onclick="closeModal('modal-add-entry')"
                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all border border-gray-100 shadow-sm">
                        <i class="fa-solid fa-times text-sm"></i>
                    </button>
                </div>
                <form id="form-add-entry" action="{{ route('petugas_entry.store') }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">Kode Petugas</label>
                            <div class="relative">
                                <input type="text" name="kode_petugas" required placeholder="Contoh: 16109999"
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-4 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm">
                                <i class="fa-solid fa-id-card absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            </div>
                            <span id="error-kode_petugas" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">Nama Petugas</label>
                            <div class="relative">
                                <input type="text" name="nama_petugas" required placeholder="Nama lengkap petugas..."
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-4 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm">
                                <i class="fa-solid fa-user absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            </div>
                            <span id="error-nama_petugas" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">Email</label>
                            <div class="relative">
                                <input type="email" name="email" required placeholder="Contoh: nama@bps.go.id"
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-4 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm">
                                <i class="fa-solid fa-envelope absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            </div>
                            <span id="error-email" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1.5 tracking-wider">No HP</label>
                            <div class="relative">
                                <input type="text" name="no_hp" required placeholder="Contoh: 0853..."
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-10 pr-4 py-2.5 text-xs font-semibold text-bps-dark focus:bg-white focus:ring-4 focus:ring-bps-orange/10 focus:border-bps-orange focus:outline-none transition-all duration-300 shadow-sm">
                                <i class="fa-solid fa-phone absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            </div>
                            <span id="error-no_hp" class="text-red-500 text-[10px] font-semibold mt-1 block error-msg hidden"></span>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 bg-gray-50/50 px-8 py-4 -mx-8 -mb-8">
                        <button type="button" onclick="closeModal('modal-add-entry')"
                            class="px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 font-bold text-xs hover:bg-gray-100 hover:text-gray-800 active:scale-95 transition-all shadow-sm">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-bps-orange to-orange-500 text-white font-extrabold text-xs shadow-md shadow-bps-orange/20 hover:shadow-lg hover:shadow-bps-orange/30 hover:brightness-105 active:scale-95 transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-sm"></i> Simpan Petugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit DSSLS Modal: disembunyikan untuk adminipds --}}
    @if (!$isAdminIpds)
        <div id="modal-dssls" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('modal-dssls')"></div>
            <div class="glass relative rounded-md w-full max-w-xl overflow-hidden shadow-2xl scale-in">
                <div class="px-8 py-6 bg-white border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Edit Data DSSLS</h3>
                        <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest font-semibold">Update Detail
                            Informasi</p>
                    </div>
                    <button type="button" onclick="closeModal('modal-dssls')"
                        class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 text-gray-500 transition-all">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
                <form action="{{ route('data_dssls.update') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="id" id="dssls-id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Jml
                                Keluarga</label>
                            <input type="number" name="perkiraan_jumlah_keluarga" id="dssls-jml-kel"
                                class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange focus:border-bps-orange">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Sampel
                                Seruti</label>
                            <input type="number" name="sampel_seruti" id="dssls-sampel"
                                class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange focus:border-bps-orange">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Penugasan
                            Petugas</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if ($isSuperAdmin || $isAdminSosial)
                                <select name="petugas_ppl" id="dssls-ppl"
                                    class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                                    <option value="">-- Pilih PPL --</option>
                                </select>
                                <select name="petugas_pml" id="dssls-pml"
                                    class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                                    <option value="">-- Pilih PML --</option>
                                </select>
                            @endif
                        </div>
                        @if ($isSuperAdmin)
                            <select name="petugas_entry" id="dssls-entry"
                                class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                                <option value="">-- Pilih Petugas Entry --</option>
                            </select>
                        @endif
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="closeModal('modal-dssls')"
                            class="px-6 py-3 rounded-lg bg-gray-100 text-gray-500 font-bold text-sm hover:bg-gray-200 transition-all">Batal</button>
                        <button type="submit"
                            class="px-8 py-3 rounded-lg bg-bps-orange text-white font-bold text-sm shadow-sm shadow-bps-orange/30 hover:bg-seorange-600 transition-all">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit DSRT Modal --}}
    <div id="modal-dsrt" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('modal-dsrt')"></div>
        <div class="glass relative rounded-md w-full max-w-xl overflow-hidden shadow-2xl scale-in">
            <div class="px-8 py-6 bg-white border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Edit Data DSRT</h3>
                    <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest font-semibold">Update Detail Informasi
                    </p>
                </div>
                <button type="button" onclick="closeModal('modal-dsrt')"
                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 text-gray-500 transition-all">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            @if ($isAdminIpds)
                <div class="p-8 text-center text-gray-400 text-sm italic">
                    <i class="fa-solid fa-lock mr-2 text-gray-300"></i>Anda hanya dapat melihat data ini.
                </div>
            @else
                <form action="{{ route('data_dsrt.update') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="id" id="dsrt-id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">R503</label>
                            <input type="text" name="r503" id="dsrt-r503"
                                class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">R503B</label>
                            <input type="text" name="r503b" id="dsrt-r503b"
                                class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Penugasan
                            Petugas</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <select name="petugas_ppl" id="dsrt-ppl"
                                class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                                <option value="">-- Pilih PPL --</option>
                            </select>
                            <select name="petugas_pml" id="dsrt-pml"
                                class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                                <option value="">-- Pilih PML --</option>
                            </select>
                        </div>
                        @if ($isSuperAdmin)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <select name="petugas_susenas" id="dsrt-susenas"
                                    class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                                    <option value="">-- Pilih Susenas --</option>
                                </select>
                                <select name="petugas_seruti" id="dsrt-seruti"
                                    class="block w-full rounded-md bg-gray-50 border border-gray-200 p-4 text-sm focus:ring-bps-orange">
                                    <option value="">-- Pilih Seruti --</option>
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="closeModal('modal-dsrt')"
                            class="px-6 py-3 rounded-lg bg-gray-100 text-gray-500 font-bold text-sm hover:bg-gray-200 transition-all">Batal</button>
                        <button type="submit"
                            class="px-8 py-3 rounded-lg bg-bps-orange text-white font-bold text-sm shadow-sm shadow-bps-orange/30 hover:bg-seorange-600 transition-all">Simpan
                            Perubahan</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

@endsection

@include('dashboard.scripts')

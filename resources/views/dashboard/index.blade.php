@extends('layouts.app')

@php
    $activeTab = session('active_tab', 'lapangan');
    $isSuperAdmin = Auth::user()->isSuperAdmin();
    $isAdminIpds = Auth::user()->isAdminIpds();
    $isAdminSosial = Auth::user()->isAdminSosial();
@endphp

@section('content')
    <div class="space-y-8">

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
                            @if ($isSuperAdmin || $isAdminSosial)
                                <div class="flex gap-2">
                                    <button type="button" onclick="deleteSelected('lapangan')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                        <i class="fa-solid fa-trash-can mr-2"></i>Hapus Terpilih
                                    </button>
                                    <button type="button" onclick="deleteAll('lapangan')"
                                        class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                        <i class="fa-solid fa-dumpster mr-2"></i>Reset
                                    </button>
                                </div>
                            @endif
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
                            @if ($isSuperAdmin || $isAdminIpds)
                                <div class="flex gap-2">
                                    <button type="button" onclick="deleteSelected('entry')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                        <i class="fa-solid fa-trash-can mr-2"></i>Hapus Terpilih
                                    </button>
                                    <button type="button" onclick="deleteAll('entry')"
                                        class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                        <i class="fa-solid fa-dumpster mr-2"></i>Reset
                                    </button>
                                </div>
                            @endif
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
                <div class="flex flex-col gap-4">
                    {{-- Baris atas: judul + mode lihat --}}
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-bps-dark">Data DSSLS</h3>
                            <p class="text-gray-500 text-sm">Monitoring progres pemuktahiran DSSLS</p>
                        </div>
                        @if ($isAdminIpds)
                            <span class="text-xs text-gray-400 italic">
                                <i class="fa-solid fa-eye mr-1"></i>Mode Lihat Saja
                            </span>
                        @endif
                    </div>

                    {{-- Baris bawah: semua tombol aksi --}}
                    @if (!$isAdminIpds)
                        <div class="flex flex-wrap items-center gap-3">

                            {{-- Hapus --}}
                            <div class="flex gap-2">
                                <button type="button" onclick="deleteSelected('dssls')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-trash-can mr-2"></i>Hapus Terpilih
                                </button>
                                <button type="button" onclick="deleteAll('dssls')"
                                    class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-dumpster mr-2"></i>Reset
                                </button>
                            </div>

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
                                <i class="fa-solid fa-file-excel mr-2"></i>Export DSSLS Pemutakhiran Sosial
                            </a>

                        </div>
                    @endif
                </div>
            </div>

            <div class="glass p-6 rounded-md overflow-x-auto">
                <table id="dt-dssls" class="display nowrap w-full" style="width:100%">
                    <thead>
                        <tr>
                            {{-- Checkbox & Action: hanya non-adminipds --}}
                            @if (!$isAdminIpds)
                                <th class="w-10 text-center"><input type="checkbox" id="selectAllDssls"
                                        class="w-4 h-4 rounded-md border-gray-300"></th>
                                <th>Action</th>
                            @endif
                            <th>Wilayah</th>
                            <th>SLS</th>
                            <th>Keluarga Awal</th>
                            <th>Keluarga Hasil Updating</th>
                            <th>Ruta Hasil Updating</th>
                            <th class="text-center">Lapangan</th>
                            <th class="text-center">Sosial</th>
                            <th class="text-center">IPDS</th>
                            {{-- PPL & PML: superadmin dan adminsosial --}}
                            @if ($isSuperAdmin || $isAdminSosial)
                                <th>PPL</th>
                                <th>PML</th>
                            @endif
                            {{-- Entry: superadmin dan adminipds --}}
                            @if ($isSuperAdmin || $isAdminIpds)
                                <th>Entry</th>
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
                            <div class="flex gap-2">
                                <button type="button" onclick="deleteSelected('dsrt')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-trash-can mr-2"></i>Hapus Terpilih
                                </button>
                                <button type="button" onclick="deleteAll('dsrt')"
                                    class="bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-dumpster mr-2"></i>Reset
                                </button>
                            </div>
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

                        {{-- Export IPDS: superadmin & adminipds --}}
                        @if ($isSuperAdmin || $isAdminIpds)
                            <a href="{{ route('data_dsrt.export_ipds') }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                <i class="fa-solid fa-file-excel mr-2"></i>Export IPDS
                            </a>
                        @endif

                        {{-- Export Sosial: superadmin & adminsosial --}}
                        @if ($isSuperAdmin || $isAdminSosial)
                            <a href="{{ route('data_dsrt.export_sosial') }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                <i class="fa-solid fa-file-excel mr-2"></i>Export Sosial Penerimaan oleh Kab
                            </a>
                            <a href="{{ route('data_dsrt.export_sosial_kab') }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                <i class="fa-solid fa-file-excel mr-2"></i>Export Sosial Pengiriman ke Kab
                            </a>
                            <a href="{{ route('data_dsrt.export_lapangan') }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                <i class="fa-solid fa-file-excel mr-2"></i>Export Lapangan
                            </a>
                            <a href="{{ route('data_dsrt.export_pemeriksaan') }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-xs font-bold transition-all shadow-sm">
                                <i class="fa-solid fa-file-excel mr-2"></i>Export Pemeriksaan
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-md overflow-x-auto">
                <table id="dt-dsrt" class="display nowrap w-full" style="width:100%">
                    <thead>
                        <tr>
                            {{-- Checkbox: hanya superadmin --}}
                            @if ($isSuperAdmin)
                                <th class="w-10 text-center"><input type="checkbox" id="selectAllDsrt"
                                        class="w-4 h-4 rounded-md border-gray-300"></th>
                            @endif
                            <th>Action</th>
                            <th>Wilayah</th>
                            <th>NBS/NKS</th>
                            <th>KRT</th>

                            <th class="text-center">Lapangan</th>
                            <th class="text-center">Sosial</th>

                            {{-- IPDS kolom posisi normal (superadmin & adminipds) --}}
                            @if ($isSuperAdmin || $isAdminIpds)
                                <th class="text-center">IPDS</th>
                            @endif

                            <th class="text-center">Pemeriksaan</th>

                            {{-- PPL & PML: superadmin dan adminsosial --}}
                            @if ($isSuperAdmin || $isAdminSosial)
                                <th>PPL</th>
                                <th>PML</th>
                            @endif

                            {{-- Susenas & Seruti: superadmin dan adminipds --}}
                            @if ($isSuperAdmin || $isAdminIpds)
                                <th>Susenas</th>
                                <th>Seruti</th>
                            @endif

                            {{-- R203 s/d Catatan KP: superadmin dan adminsosial (IPDS disembunyikan) --}}
                            @if ($isSuperAdmin || $isAdminSosial)
                                <th>R203 KOR</th>
                                <th>R203 KP</th>
                                <th>R301 Jml ART</th>
                                <th>R304 (VSEN26-KP)</th>
                                <th>R305 (VSEN26-KP)</th>
                                <th>Catatan KOR</th>
                                <th>Catatan KP</th>
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

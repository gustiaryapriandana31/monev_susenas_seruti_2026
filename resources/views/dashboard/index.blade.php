@extends('layouts.app')

@section('content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="p-6 bg-gradient-to-r from-seorange-500 to-seorange-600 text-white flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold">Dashboard Monitoring</h2>
            <p class="text-seorange-100 text-sm mt-1">Sensus Ekonomi 2026 - Pengawasan Petugas dan Progress Lapangan</p>
        </div>
    </div>

    <!-- Tabs Nav -->
    <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="dashboardTabs" data-tabs-toggle="#tabContent" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-seorange-600 hover:border-seorange-300 text-seorange-600 border-seorange-600 tab-button" id="tab-lapangan" data-target="#content-lapangan" type="button" role="tab" aria-controls="content-lapangan" aria-selected="true">
                    <i class="fa-solid fa-users mr-1"></i> Data Petugas Lapangan
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-seorange-600 hover:border-seorange-300 tab-button" id="tab-entry" data-target="#content-entry" type="button" role="tab" aria-controls="content-entry" aria-selected="false">
                    <i class="fa-solid fa-user-edit mr-1"></i> Data Petugas Entry
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-seorange-600 hover:border-seorange-300 tab-button" id="tab-dssls" data-target="#content-dssls" type="button" role="tab" aria-controls="content-dssls" aria-selected="false">
                    <i class="fa-solid fa-map-location-dot mr-1"></i> Data DSSLS
                </button>
            </li>
            <li role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-seorange-600 hover:border-seorange-300 tab-button" id="tab-dsrt" data-target="#content-dsrt" type="button" role="tab" aria-controls="content-dsrt" aria-selected="false">
                    <i class="fa-solid fa-file-contract mr-1"></i> Data DSRT
                </button>
            </li>
        </ul>
    </div>

    <!-- Tabs Content -->
    <div id="tabContent" class="p-6">
        
        <!-- Tab: Petugas Lapangan -->
        <div class="tab-pane" id="content-lapangan" role="tabpanel" aria-labelledby="tab-lapangan">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Daftar Petugas Lapangan</h3>
                <form action="{{ route('petugas_lapangan.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-sm">
                    @csrf
                    <input type="file" name="file_excel" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-seorange-50 file:text-seorange-700 hover:file:bg-seorange-100 cursor-pointer" accept=".xlsx,.xls,.csv" required>
                    <button type="submit" class="bg-seorange-500 hover:bg-seorange-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                        <i class="fa-solid fa-upload mr-1"></i> Import
                    </button>
                </form>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table id="dt-lapangan" class="display responsive nowrap w-full text-sm text-left text-gray-500" style="width:100%">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th>Kode</th>
                            <th>Provinsi</th>
                            <th>Kabupaten</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Kode Jabatan</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($petugasLapangans as $pl)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="font-medium text-gray-900">{{ $pl->kode_petugas }}</td>
                            <td>{{ $pl->provinsi }}</td>
                            <td>{{ $pl->kabupaten }}</td>
                            <td>{{ $pl->nama_petugas }}</td>
                            <td>{{ $pl->no_hp }}</td>
                            <td>{{ $pl->kode_jabatan }}</td>
                            <td>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $pl->jabatan }}</span>
                            </td>
                            <td>
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $pl->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Petugas Entry -->
        <div class="tab-pane hidden" id="content-entry" role="tabpanel" aria-labelledby="tab-entry">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Daftar Petugas Entry</h3>
                <form action="{{ route('petugas_entry.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-sm">
                    @csrf
                    <input type="file" name="file_excel" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-seorange-50 file:text-seorange-700 hover:file:bg-seorange-100 cursor-pointer" accept=".xlsx,.xls,.csv" required>
                    <button type="submit" class="bg-seorange-500 hover:bg-seorange-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                        <i class="fa-solid fa-upload mr-1"></i> Import
                    </button>
                </form>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table id="dt-entry" class="display responsive nowrap w-full text-sm text-left text-gray-500" style="width:100%">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th>Kode</th>
                            <th>Provinsi</th>
                            <th>Kabupaten</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($petugasEntries as $pe)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="font-medium text-gray-900">{{ $pe->kode_petugas }}</td>
                            <td>{{ $pe->provinsi }}</td>
                            <td>{{ $pe->kabupaten }}</td>
                            <td>{{ $pe->nama_petugas }}</td>
                            <td>{{ $pe->email }}</td>
                            <td>{{ $pe->no_hp }}</td>
                            <td>
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $pe->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Data DSSLS -->
        <div class="tab-pane hidden" id="content-dssls" role="tabpanel" aria-labelledby="tab-dssls">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Data Pemuktahiran DSSLS</h3>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="deleteSelected('dssls')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium transition duration-150">
                        <i class="fa-solid fa-trash mr-1"></i> Hapus Terpilih
                    </button>
                    <button type="button" onclick="deleteAll('dssls')" class="bg-red-700 hover:bg-red-800 text-white px-3 py-2 rounded-md text-sm font-medium transition duration-150">
                        <i class="fa-solid fa-dumpster mr-1"></i> Hapus Semua
                    </button>
                    <form action="{{ route('data_dssls.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-sm">
                        @csrf
                        <input type="file" name="file_excel" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-seorange-50 file:text-seorange-700 hover:file:bg-seorange-100 cursor-pointer" accept=".xlsx,.xls,.csv" required>
                        <button type="submit" class="bg-seorange-500 hover:bg-seorange-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                            <i class="fa-solid fa-upload mr-1"></i> Import
                        </button>
                    </form>
                    <a href="{{ route('data_dssls.export') }}" class="flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                        <i class="fa-solid fa-file-excel"></i> Export
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table id="dt-dssls" class="display responsive nowrap w-full text-sm text-left text-gray-500" style="width:100%">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="w-10 text-center"><input type="checkbox" id="selectAllDssls" class="w-4 h-4 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500"></th>
                            <th>Action</th>
                            <th>Prov</th>
                            <th>Kab</th>
                            <th>Kec</th>
                            <th>Desa/Kel</th>
                            <th>Kode SLS</th>
                            <th>Nama SLS</th>
                            <th>Ceklis Lapangan</th>
                            <th>Ceklis Sosial</th>
                            <th>Ceklis IPDS</th>
                            <th>PPL</th>
                            <th>PML</th>
                            <th>Entry</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataDssls as $ds)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="text-center align-middle">
                                <input type="checkbox" class="row-dssls-checkbox w-4 h-4 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500" value="{{ $ds->id }}">
                            </td>
                            <td>
                                <button onclick="editDssls(this)" data-item="{{ json_encode($ds) }}" class="text-seorange-600 hover:text-seorange-800" title="Edit Data">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                            </td>
                            <td>{{ $ds->provinsi }}</td>
                            <td>{{ $ds->kabupaten }}</td>
                            <td>{{ $ds->kecamatan }}</td>
                            <td>{{ $ds->nama_desa_kelurahan }}</td>
                            <td class="font-medium text-gray-900">{{ $ds->kode_sls }}</td>
                            <td>{{ $ds->nama_sls }}</td>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="ceklis-toggle w-5 h-5 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500" 
                                       data-id="{{ $ds->id }}" data-type="dssls" data-field="ceklis_lap" 
                                       {{ $ds->ceklis_lap ? 'checked' : '' }}>
                                <div class="text-[10px] text-gray-500 mt-1 mt-1" id="lbl-lap-dssls-{{ $ds->id }}">
                                    {{ $ds->waktu_ceklis_lap ? \Carbon\Carbon::parse($ds->waktu_ceklis_lap)->format('d/m/y H:i') : '' }}
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="ceklis-toggle w-5 h-5 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500" 
                                       data-id="{{ $ds->id }}" data-type="dssls" data-field="ceklis_sosial" 
                                       {{ $ds->ceklis_sosial ? 'checked' : '' }}>
                                <div class="text-[10px] text-gray-500 mt-1 mt-1" id="lbl-sosial-dssls-{{ $ds->id }}">
                                    {{ $ds->waktu_ceklis_sosial ? \Carbon\Carbon::parse($ds->waktu_ceklis_sosial)->format('d/m/y H:i') : '' }}
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="ceklis-toggle w-5 h-5 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500" 
                                       data-id="{{ $ds->id }}" data-type="dssls" data-field="ceklis_ipds" 
                                       {{ $ds->ceklis_ipds ? 'checked' : '' }}>
                                <div class="text-[10px] text-gray-500 mt-1 mt-1" id="lbl-ipds-dssls-{{ $ds->id }}">
                                    {{ $ds->waktu_ceklis_ipds ? \Carbon\Carbon::parse($ds->waktu_ceklis_ipds)->format('d/m/y H:i') : '' }}
                                </div>
                            </td>
                            <td>
                                <select class="petugas-dssls-update text-xs border border-gray-300 rounded p-1 w-full focus:ring-seorange-500 focus:border-seorange-500" data-id="{{ $ds->id }}" data-field="petugas_ppl">
                                    <option value="">- PPL -</option>
                                    @foreach($petugasLapangans->where('jabatan', 'Pencacah (PPL)') as $ppl)
                                        <option value="{{ $ppl->kode_petugas }}" {{ ($ds->petugas_ppl == $ppl->kode_petugas) || (!empty($ds->ppl) && $ds->ppl->nama_petugas == $ppl->nama_petugas) ? 'selected' : '' }}>
                                            {{ $ppl->kode_petugas }} - {{ $ppl->nama_petugas }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="petugas-dssls-update text-xs border border-gray-300 rounded p-1 w-full focus:ring-seorange-500 focus:border-seorange-500" data-id="{{ $ds->id }}" data-field="petugas_pml">
                                    <option value="">- PML -</option>
                                    @foreach($petugasLapangans->where('jabatan', 'Pengawas (PML)') as $pml)
                                        <option value="{{ $pml->kode_petugas }}" {{ ($ds->petugas_pml == $pml->kode_petugas) || (!empty($ds->pml) && $ds->pml->nama_petugas == $pml->nama_petugas) ? 'selected' : '' }}>
                                            {{ $pml->kode_petugas }} - {{ $pml->nama_petugas }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="petugas-dssls-update text-xs border border-gray-300 rounded p-1 w-full focus:ring-seorange-500 focus:border-seorange-500" data-id="{{ $ds->id }}" data-field="petugas_entry">
                                    <option value="">- Entry -</option>
                                    @foreach($petugasEntries as $entry)
                                        <option value="{{ $entry->kode_petugas }}" {{ ($ds->petugas_entry == $entry->kode_petugas) || (!empty($ds->entry) && $ds->entry->nama_petugas == $entry->nama_petugas) ? 'selected' : '' }}>
                                            {{ $entry->kode_petugas }} - {{ $entry->nama_petugas }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Data DSRT -->
        <div class="tab-pane hidden" id="content-dsrt" role="tabpanel" aria-labelledby="tab-dsrt">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Data Pemuktahiran DSRT</h3>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="deleteSelected('dsrt')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium transition duration-150">
                        <i class="fa-solid fa-trash mr-1"></i> Hapus Terpilih
                    </button>
                    <button type="button" onclick="deleteAll('dsrt')" class="bg-red-700 hover:bg-red-800 text-white px-3 py-2 rounded-md text-sm font-medium transition duration-150">
                        <i class="fa-solid fa-dumpster mr-1"></i> Hapus Semua
                    </button>
                    <form action="{{ route('data_dsrt.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-sm">
                        @csrf
                        <input type="file" name="file_excel" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-seorange-50 file:text-seorange-700 hover:file:bg-seorange-100 cursor-pointer" accept=".xlsx,.xls,.csv" required>
                        <button type="submit" class="bg-seorange-500 hover:bg-seorange-600 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                            <i class="fa-solid fa-upload mr-1"></i> Import
                        </button>
                    </form>
                    <a href="{{ route('data_dsrt.export') }}" class="flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                        <i class="fa-solid fa-file-excel"></i> Export
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table id="dt-dsrt" class="display responsive nowrap w-full text-sm text-left text-gray-500" style="width:100%">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="w-10 text-center"><input type="checkbox" id="selectAllDsrt" class="w-4 h-4 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500"></th>
                            <th>Action</th>
                            <th>Kec</th>
                            <th>Desa</th>
                            <th>NBS</th>
                            <th>NKS SAK22</th>
                            <th>KRT</th>
                            <th>Ceklis Lapangan</th>
                            <th>Ceklis Sosial</th>
                            <th>Ceklis IPDS</th>
                            <th>PPL</th>
                            <th>PML</th>
                            <th>Susenas</th>
                            <th>Seruti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataDsrt as $rt)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="text-center align-middle">
                                <input type="checkbox" class="row-dsrt-checkbox w-4 h-4 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500" value="{{ $rt->id }}">
                            </td>
                            <td>
                                <button onclick="editDsrt(this)" data-item="{{ json_encode($rt) }}" class="text-seorange-600 hover:text-seorange-800" title="Edit Data">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                            </td>
                            <td>{{ $rt->kec }}</td>
                            <td>{{ $rt->desa }}</td>
                            <td class="font-medium text-gray-900">{{ $rt->nmslsm }}</td>
                            <td>{{ $rt->nks_sak22 }}</td>
                            <td>{{ $rt->r503 }}</td>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="ceklis-toggle w-5 h-5 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500" 
                                       data-id="{{ $rt->id }}" data-type="dsrt" data-field="ceklis_lap" 
                                       {{ $rt->ceklis_lap ? 'checked' : '' }}>
                                <div class="text-[10px] text-gray-500 mt-1" id="lbl-lap-dsrt-{{ $rt->id }}">
                                    {{ $rt->waktu_ceklis_lap ? \Carbon\Carbon::parse($rt->waktu_ceklis_lap)->format('d/m/y H:i') : '' }}
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="ceklis-toggle w-5 h-5 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500" 
                                       data-id="{{ $rt->id }}" data-type="dsrt" data-field="ceklis_sosial" 
                                       {{ $rt->ceklis_sosial ? 'checked' : '' }}>
                                <div class="text-[10px] text-gray-500 mt-1" id="lbl-sosial-dsrt-{{ $rt->id }}">
                                    {{ $rt->waktu_ceklis_sosial ? \Carbon\Carbon::parse($rt->waktu_ceklis_sosial)->format('d/m/y H:i') : '' }}
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="ceklis-toggle w-5 h-5 text-seorange-600 bg-gray-100 border-gray-300 rounded focus:ring-seorange-500" 
                                       data-id="{{ $rt->id }}" data-type="dsrt" data-field="ceklis_ipds" 
                                       {{ $rt->ceklis_ipds ? 'checked' : '' }}>
                                <div class="text-[10px] text-gray-500 mt-1" id="lbl-ipds-dsrt-{{ $rt->id }}">
                                    {{ $rt->waktu_ceklis_ipds ? \Carbon\Carbon::parse($rt->waktu_ceklis_ipds)->format('d/m/y H:i') : '' }}
                                </div>
                            </td>
                            <td>
                                <select class="petugas-dsrt-update text-xs border border-gray-300 rounded p-1 w-full focus:ring-seorange-500 focus:border-seorange-500" data-id="{{ $rt->id }}" data-field="petugas_ppl">
                                    <option value="">- PPL -</option>
                                    @foreach($petugasLapangans->where('jabatan', 'Pencacah (PPL)') as $ppl)
                                        <option value="{{ $ppl->kode_petugas }}" {{ ($rt->petugas_ppl == $ppl->kode_petugas) || (!empty($rt->ppl) && $rt->ppl->nama_petugas == $ppl->nama_petugas) ? 'selected' : '' }}>
                                            {{ $ppl->kode_petugas }} - {{ $ppl->nama_petugas }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="petugas-dsrt-update text-xs border border-gray-300 rounded p-1 w-full focus:ring-seorange-500 focus:border-seorange-500" data-id="{{ $rt->id }}" data-field="petugas_pml">
                                    <option value="">- PML -</option>
                                    @foreach($petugasLapangans->where('jabatan', 'Pengawas (PML)') as $pml)
                                        <option value="{{ $pml->kode_petugas }}" {{ ($rt->petugas_pml == $pml->kode_petugas) || (!empty($rt->pml) && $rt->pml->nama_petugas == $pml->nama_petugas) ? 'selected' : '' }}>
                                            {{ $pml->kode_petugas }} - {{ $pml->nama_petugas }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="petugas-dsrt-update text-xs border border-gray-300 rounded p-1 w-full focus:ring-seorange-500 focus:border-seorange-500" data-id="{{ $rt->id }}" data-field="petugas_susenas">
                                    <option value="">- Susenas -</option>
                                    @foreach($petugasEntries as $susenas)
                                        <option value="{{ $susenas->kode_petugas }}" {{ ($rt->petugas_susenas == $susenas->kode_petugas) ? 'selected' : '' }}>
                                            {{ $susenas->kode_petugas }} - {{ $susenas->nama_petugas }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="petugas-dsrt-update text-xs border border-gray-300 rounded p-1 w-full focus:ring-seorange-500 focus:border-seorange-500" data-id="{{ $rt->id }}" data-field="petugas_seruti">
                                    <option value="">- Seruti -</option>
                                    @foreach($petugasEntries as $seruti)
                                        <option value="{{ $seruti->kode_petugas }}" {{ ($rt->petugas_seruti == $seruti->kode_petugas) ? 'selected' : '' }}>
                                            {{ $seruti->kode_petugas }} - {{ $seruti->nama_petugas }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Edit DSSLS Modal -->
    <div id="modal-dssls" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 bg-seorange-500 text-white flex justify-between items-center">
                <h3 class="text-lg font-semibold">Edit Data DSSLS</h3>
                <button type="button" onclick="closeModal('modal-dssls')" class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form action="{{ route('data_dssls.update') }}" method="POST">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" name="id" id="dssls-id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Perkiraan Jumlah Keluarga</label>
                        <input type="number" name="perkiraan_jumlah_keluarga" id="dssls-jml-kel" class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 border p-2 focus:ring-seorange-500 focus:border-seorange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sampel Seruti</label>
                        <input type="number" name="sampel_seruti" id="dssls-sampel" class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 border p-2 focus:ring-seorange-500 focus:border-seorange-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Petugas PPL</label>
                            <select name="petugas_ppl" id="dssls-ppl" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm focus:border-seorange-500 focus:ring-seorange-500 p-2 border">
                                <option value="">-- Pilih PPL --</option>
                                @foreach($petugasLapangans->where('jabatan', 'Pencacah (PPL)') as $ppl)
                                    <option value="{{ $ppl->kode_petugas }}">{{ $ppl->kode_petugas }} - {{ $ppl->nama_petugas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Petugas PML</label>
                            <select name="petugas_pml" id="dssls-pml" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm focus:border-seorange-500 focus:ring-seorange-500 p-2 border">
                                <option value="">-- Pilih PML --</option>
                                @foreach($petugasLapangans->where('jabatan', 'Pengawas (PML)') as $pml)
                                    <option value="{{ $pml->kode_petugas }}">{{ $pml->kode_petugas }} - {{ $pml->nama_petugas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Petugas Entry</label>
                        <select name="petugas_entry" id="dssls-entry" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm focus:border-seorange-500 focus:ring-seorange-500 p-2 border">
                            <option value="">-- Pilih Petugas Entry --</option>
                            @foreach($petugasEntries as $entry)
                                <option value="{{ $entry->kode_petugas }}">{{ $entry->kode_petugas }} - {{ $entry->nama_petugas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-dssls')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-seorange-500 text-white rounded-md hover:bg-seorange-600">Simpan Detail</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit DSRT Modal -->
    <div id="modal-dsrt" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 bg-seorange-500 text-white flex justify-between items-center">
                <h3 class="text-lg font-semibold">Edit Data DSRT</h3>
                <button type="button" onclick="closeModal('modal-dsrt')" class="text-white hover:text-gray-200 focus:outline-none">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form action="{{ route('data_dsrt.update') }}" method="POST">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" name="id" id="dsrt-id">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">R503</label>
                            <input type="text" name="r503" id="dsrt-r503" class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 border p-2 focus:ring-seorange-500 focus:border-seorange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">R503B</label>
                            <input type="text" name="r503b" id="dsrt-r503b" class="mt-1 flex-1 block w-full rounded-md sm:text-sm border-gray-300 border p-2 focus:ring-seorange-500 focus:border-seorange-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Petugas PPL</label>
                            <select name="petugas_ppl" id="dsrt-ppl" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm focus:border-seorange-500 focus:ring-seorange-500 p-2 border">
                                <option value="">-- Pilih PPL --</option>
                                @foreach($petugasLapangans->where('jabatan', 'Pencacah (PPL)') as $ppl)
                                    <option value="{{ $ppl->kode_petugas }}">{{ $ppl->kode_petugas }} - {{ $ppl->nama_petugas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Petugas PML</label>
                            <select name="petugas_pml" id="dsrt-pml" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm focus:border-seorange-500 focus:ring-seorange-500 p-2 border">
                                <option value="">-- Pilih PML --</option>
                                @foreach($petugasLapangans->where('jabatan', 'Pengawas (PML)') as $pml)
                                    <option value="{{ $pml->kode_petugas }}">{{ $pml->kode_petugas }} - {{ $pml->nama_petugas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Petugas Susenas</label>
                            <select name="petugas_susenas" id="dsrt-susenas" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm focus:border-seorange-500 focus:ring-seorange-500 p-2 border">
                                <option value="">-- Pilih Susenas --</option>
                                @foreach($petugasEntries as $susenas)
                                    <option value="{{ $susenas->kode_petugas }}">{{ $susenas->kode_petugas }} - {{ $susenas->nama_petugas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Petugas Seruti</label>
                            <select name="petugas_seruti" id="dsrt-seruti" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm focus:border-seorange-500 focus:ring-seorange-500 p-2 border">
                                <option value="">-- Pilih Seruti --</option>
                                @foreach($petugasEntries as $seruti)
                                    <option value="{{ $seruti->kode_petugas }}">{{ $seruti->kode_petugas }} - {{ $seruti->nama_petugas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-dsrt')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-seorange-500 text-white rounded-md hover:bg-seorange-600">Simpan Detail</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables
        const dtConfigs = {
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        };

        $('#dt-lapangan').DataTable(dtConfigs);
        $('#dt-entry').DataTable(dtConfigs);
        $('#dt-dssls').DataTable(dtConfigs);
        $('#dt-dsrt').DataTable(dtConfigs);

        // Select All Checkbox Logic
        $('#selectAllDssls').on('change', function() {
            $('.row-dssls-checkbox').prop('checked', $(this).is(':checked'));
        });
        $('#selectAllDsrt').on('change', function() {
            $('.row-dsrt-checkbox').prop('checked', $(this).is(':checked'));
        });

        // Tab Switching Logic
        $('.tab-button').on('click', function() {
            // Remove active classes
            $('.tab-button').removeClass('text-seorange-600 border-seorange-600').addClass('border-transparent');
            $('.tab-pane').addClass('hidden');

            // Add active class to clicked tab
            $(this).removeClass('border-transparent').addClass('text-seorange-600 border-seorange-600');
            $($(this).data('target')).removeClass('hidden');

            // Force DataTables to adjust columns when tab is shown
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        // Inline Petugas Update Logic (DSSLS & DSRT)
        $('.petugas-dssls-update, .petugas-dsrt-update').on('change', function() {
            let selectedValue = $(this).val();
            let id = $(this).data('id');
            let field = $(this).data('field'); // petugas_ppl, petugas_pml
            let type = $(this).hasClass('petugas-dssls-update') ? 'dssls' : 'dsrt';
            let targetUrl = type === 'dssls' ? '/data-dssls/update-inline' : '/data-dsrt/update-inline';

            // Show slight opacity while updating
            $(this).css('opacity', '0.5');

            $.ajax({
                url: targetUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    field: field,
                    value: selectedValue
                },
                success: function(response) {
                    if(!response.success) {
                        Swal.fire('Error', response.message || 'Gagal menyimpan perubahan petugas', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                },
                complete: () => {
                    $(this).css('opacity', '1');
                }
            });
        });

        // AJAX Checkbox Toggle
        $('.ceklis-toggle').on('change', function() {
            let isChecked = $(this).is(':checked') ? 1 : 0;
            let id = $(this).data('id');
            let type = $(this).data('type'); // dssls or dsrt
            let field = $(this).data('field'); // ceklis_lap, ceklis_sosial, ceklis_ipds

            let targetUrl = type === 'dssls' ? '/data-dssls/toggle-ceklis' : '/data-dsrt/toggle-ceklis';

            // Show slight opacity while updating
            $(this).parent().css('opacity', '0.5');

            $.ajax({
                url: targetUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    field: field,
                    state: isChecked
                },
                success: function(response) {
                    if(response.success) {
                        // Update time label natively based on type and field
                        let timeLabelId = `#lbl-${field.replace('ceklis_', '')}-${type}-${id}`;
                        $(timeLabelId).text(response.timestamp || '');
                    } else {
                        Swal.fire('Error', 'Gagal update status ceklis', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                },
                complete: () => {
                    $(this).parent().css('opacity', '1');
                }
            });
        });
    });

    // Modal Functions
    function closeModal(id) {
        $(`#${id}`).addClass('hidden');
    }

    function editDssls(button) {
        let item = JSON.parse($(button).attr('data-item'));
        
        // Grab latest inline selections directly from the DOM row
        let tr = $(button).closest('tr');
        let latestPpl = tr.find('select[data-field="petugas_ppl"]').val();
        let latestPml = tr.find('select[data-field="petugas_pml"]').val();
        let latestEntry = tr.find('select[data-field="petugas_entry"]').val();

        $('#dssls-id').val(item.id);
        $('#dssls-jml-kel').val(item.perkiraan_jumlah_keluarga);
        $('#dssls-sampel').val(item.sampel_seruti);
        
        $('#dssls-ppl').val(latestPpl !== undefined ? latestPpl : item.petugas_ppl);
        $('#dssls-pml').val(latestPml !== undefined ? latestPml : item.petugas_pml);
        $('#dssls-entry').val(latestEntry !== undefined ? latestEntry : item.petugas_entry);
        
        $('#modal-dssls').removeClass('hidden');
    }

    function editDsrt(button) {
        let item = JSON.parse($(button).attr('data-item'));
        
        // Grab latest inline selections directly from the DOM row
        let tr = $(button).closest('tr');
        let latestPpl = tr.find('select[data-field="petugas_ppl"]').val();
        let latestPml = tr.find('select[data-field="petugas_pml"]').val();
        let latestSusenas = tr.find('select[data-field="petugas_susenas"]').val();
        let latestSeruti = tr.find('select[data-field="petugas_seruti"]').val();

        $('#dsrt-id').val(item.id);
        $('#dsrt-r503').val(item.r503);
        $('#dsrt-r503b').val(item.r503b);
        
        $('#dsrt-ppl').val(latestPpl !== undefined ? latestPpl : item.petugas_ppl);
        $('#dsrt-pml').val(latestPml !== undefined ? latestPml : item.petugas_pml);
        $('#dsrt-susenas').val(latestSusenas !== undefined ? latestSusenas : item.petugas_susenas);
        $('#dsrt-seruti').val(latestSeruti !== undefined ? latestSeruti : item.petugas_seruti);
        
        $('#modal-dsrt').removeClass('hidden');
    }

    // Bulk Actions Functions
    function getSelectedIds(type) {
        let ids = [];
        $(`.row-${type}-checkbox:checked`).each(function() {
            ids.push($(this).val());
        });
        return ids;
    }

    function deleteSelected(type) {
        let ids = getSelectedIds(type);
        if (ids.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal satu data untuk dihapus', 'warning');
            return;
        }

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus ${ids.length} data terpilih!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/data-${type}/delete-bulk`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: ids
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Terhapus!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Gagal menghapus data', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                    }
                });
            }
        });
    }

    function deleteAll(type) {
        Swal.fire({
            title: 'Hapus SEMUA data?',
            text: "Tindakan ini tidak dapat dibatalkan! Seluruh data pada tabel ini akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus SEMUA!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/data-${type}/delete-all`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Terhapus!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Gagal menghapus semua data', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush

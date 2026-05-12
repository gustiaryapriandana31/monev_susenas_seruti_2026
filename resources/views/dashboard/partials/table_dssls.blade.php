@foreach($data as $ds)
<tr>
    <td class="text-center">
        <input type="checkbox" class="row-dssls-checkbox w-4 h-4 rounded-md border-gray-300" value="{{ $ds->id }}">
    </td>
    <td>
        <button onclick="editDssls(this)" data-item="{{ json_encode($ds) }}" class="w-8 h-8 rounded-lg bg-bps-orange/10 text-bps-orange hover:bg-bps-orange hover:text-white transition-all">
            <i class="fa-solid fa-pen-to-square"></i>
        </button>
    </td>
    <td>
        <p class="font-bold text-bps-dark text-xs">{{ $ds->nama_kecamatan }}</p>
        <p class="text-[10px] text-gray-500 uppercase">{{ $ds->nama_desa_kelurahan }}</p>
    </td>
    <td>
        <p class="font-bold text-bps-dark text-xs">{{ $ds->kode_sls }}</p>
        <p class="text-[10px] text-gray-500 uppercase truncate max-w-[150px]">{{ $ds->nama_sls }}</p>
    </td>
    <td class="text-center">
        <div class="flex flex-col items-center">
            <input type="checkbox" class="ceklis-toggle w-5 h-5 text-bps-orange rounded-lg border-gray-300 transition-all cursor-pointer" 
                   data-id="{{ $ds->id }}" data-type="dssls" data-field="ceklis_lap" 
                   {{ $ds->ceklis_lap ? 'checked' : '' }}>
            <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase" id="lbl-lap-dssls-{{ $ds->id }}">
                {{ $ds->waktu_ceklis_lap ? \Carbon\Carbon::parse($ds->waktu_ceklis_lap)->format('d/m H:i') : '' }}
            </span>
        </div>
    </td>
    <td class="text-center">
        <div class="flex flex-col items-center">
            <input type="checkbox" class="ceklis-toggle w-5 h-5 text-bps-orange rounded-lg border-gray-300 transition-all cursor-pointer" 
                   data-id="{{ $ds->id }}" data-type="dssls" data-field="ceklis_sosial" 
                   {{ $ds->ceklis_sosial ? 'checked' : '' }}>
            <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase" id="lbl-sosial-dssls-{{ $ds->id }}">
                {{ $ds->waktu_ceklis_sosial ? \Carbon\Carbon::parse($ds->waktu_ceklis_sosial)->format('d/m H:i') : '' }}
            </span>
        </div>
    </td>
    <td class="text-center">
        <div class="flex flex-col items-center">
            <input type="checkbox" class="ceklis-toggle w-5 h-5 text-bps-orange rounded-lg border-gray-300 transition-all cursor-pointer" 
                   data-id="{{ $ds->id }}" data-type="dssls" data-field="ceklis_ipds" 
                   {{ $ds->ceklis_ipds ? 'checked' : '' }}>
            <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase" id="lbl-ipds-dssls-{{ $ds->id }}">
                {{ $ds->waktu_ceklis_ipds ? \Carbon\Carbon::parse($ds->waktu_ceklis_ipds)->format('d/m H:i') : '' }}
            </span>
        </div>
    </td>
    <td>
        <select class="petugas-dssls-update lazy-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange" data-id="{{ $ds->id }}" data-field="petugas_ppl" data-selected="{{ $ds->petugas_ppl }}">
            <option value="{{ $ds->petugas_ppl }}">{{ $ds->ppl ? $ds->ppl->nama_petugas : '- PPL -' }}</option>
        </select>
    </td>
    <td>
        <select class="petugas-dssls-update lazy-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange" data-id="{{ $ds->id }}" data-field="petugas_pml" data-selected="{{ $ds->petugas_pml }}">
            <option value="{{ $ds->petugas_pml }}">{{ $ds->pml ? $ds->pml->nama_petugas : '- PML -' }}</option>
        </select>
    </td>
    <td>
        <select class="petugas-dssls-update lazy-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange" data-id="{{ $ds->id }}" data-field="petugas_entry" data-selected="{{ $ds->petugas_entry }}">
            <option value="{{ $ds->petugas_entry }}">{{ $ds->entry ? $ds->entry->nama_petugas : '- Entry -' }}</option>
        </select>
    </td>
</tr>
@endforeach

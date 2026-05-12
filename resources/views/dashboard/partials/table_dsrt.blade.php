@foreach($data as $rt)
<tr>
    <td class="text-center">
        <input type="checkbox" class="row-dsrt-checkbox w-4 h-4 rounded-md border-gray-300" value="{{ $rt->id }}">
    </td>
    <td>
        <button onclick="editDsrt(this)" data-item="{{ json_encode($rt) }}" class="w-8 h-8 rounded-lg bg-bps-orange/10 text-bps-orange hover:bg-bps-orange hover:text-white transition-all">
            <i class="fa-solid fa-pen-to-square"></i>
        </button>
    </td>
    <td>
        <p class="font-bold text-bps-dark text-xs">{{ $rt->kec }}</p>
        <p class="text-[10px] text-gray-500 uppercase">{{ $rt->desa }}</p>
    </td>
    <td>
        <p class="font-bold text-bps-dark text-xs truncate max-w-[100px]">{{ $rt->nmslsm }}</p>
        <p class="text-[10px] text-gray-500 uppercase">{{ $rt->nks_sak22 }}</p>
    </td>
    <td class="font-medium text-xs">{{ $rt->r503 }}</td>
    <td class="text-center">
        <div class="flex flex-col items-center">
            <input type="checkbox" class="ceklis-toggle w-5 h-5 text-bps-orange rounded-lg border-gray-300 transition-all cursor-pointer" 
                   data-id="{{ $rt->id }}" data-type="dsrt" data-field="ceklis_lap" 
                   {{ $rt->ceklis_lap ? 'checked' : '' }}>
            <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase" id="lbl-lap-dsrt-{{ $rt->id }}">
                {{ $rt->waktu_ceklis_lap ? \Carbon\Carbon::parse($rt->waktu_ceklis_lap)->format('d/m H:i') : '' }}
            </span>
        </div>
    </td>
    <td class="text-center">
        <div class="flex flex-col items-center">
            <input type="checkbox" class="ceklis-toggle w-5 h-5 text-bps-orange rounded-lg border-gray-300 transition-all cursor-pointer" 
                   data-id="{{ $rt->id }}" data-type="dsrt" data-field="ceklis_sosial" 
                   {{ $rt->ceklis_sosial ? 'checked' : '' }}>
            <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase" id="lbl-sosial-dsrt-{{ $rt->id }}">
                {{ $rt->waktu_ceklis_sosial ? \Carbon\Carbon::parse($rt->waktu_ceklis_sosial)->format('d/m H:i') : '' }}
            </span>
        </div>
    </td>
    <td class="text-center">
        <div class="flex flex-col items-center">
            <input type="checkbox" class="ceklis-toggle w-5 h-5 text-bps-orange rounded-lg border-gray-300 transition-all cursor-pointer" 
                   data-id="{{ $rt->id }}" data-type="dsrt" data-field="ceklis_ipds" 
                   {{ $rt->ceklis_ipds ? 'checked' : '' }}>
            <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase" id="lbl-ipds-dsrt-{{ $rt->id }}">
                {{ $rt->waktu_ceklis_ipds ? \Carbon\Carbon::parse($rt->waktu_ceklis_ipds)->format('d/m H:i') : '' }}
            </span>
        </div>
    </td>
    <td>
        <select class="petugas-dsrt-update lazy-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange" data-id="{{ $rt->id }}" data-field="petugas_ppl" data-selected="{{ $rt->petugas_ppl }}">
            <option value="{{ $rt->petugas_ppl }}">{{ $rt->ppl ? $rt->ppl->nama_petugas : '- PPL -' }}</option>
        </select>
    </td>
    <td>
        <select class="petugas-dsrt-update lazy-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange" data-id="{{ $rt->id }}" data-field="petugas_pml" data-selected="{{ $rt->petugas_pml }}">
            <option value="{{ $rt->petugas_pml }}">{{ $rt->pml ? $rt->pml->nama_petugas : '- PML -' }}</option>
        </select>
    </td>
    <td>
        <select class="petugas-dsrt-update lazy-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange" data-id="{{ $rt->id }}" data-field="petugas_susenas" data-selected="{{ $rt->petugas_susenas }}">
            <option value="{{ $rt->petugas_susenas }}">{{ $rt->susenas ? $rt->susenas->nama_petugas : '- Susenas -' }}</option>
        </select>
    </td>
    <td>
        <select class="petugas-dsrt-update lazy-select text-[10px] font-bold bg-white/50 border-white/60 rounded-xl px-2 py-1 w-full focus:ring-bps-orange" data-id="{{ $rt->id }}" data-field="petugas_seruti" data-selected="{{ $rt->petugas_seruti }}">
            <option value="{{ $rt->petugas_seruti }}">{{ $rt->seruti ? $rt->seruti->nama_petugas : '- Seruti -' }}</option>
        </select>
    </td>
</tr>
@endforeach

@foreach($data as $pl)
<tr>
    <td class="font-bold text-bps-dark">{{ $pl->kode_petugas }}</td>
    <td>{{ $pl->provinsi }}</td>
    <td>{{ $pl->kabupaten }}</td>
    <td class="font-medium">{{ $pl->nama_petugas }}</td>
    <td>{{ $pl->no_hp }}</td>
    <td>
        <span class="bg-blue-500/10 text-blue-600 text-[10px] font-bold px-3 py-1 rounded-full border border-blue-200 uppercase">{{ $pl->jabatan }}</span>
    </td>
    <td>
        <span class="bg-green-500/10 text-green-600 text-[10px] font-bold px-3 py-1 rounded-full border border-green-200 uppercase">{{ $pl->status }}</span>
    </td>
</tr>
@endforeach

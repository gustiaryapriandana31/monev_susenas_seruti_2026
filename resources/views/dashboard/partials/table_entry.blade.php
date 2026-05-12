@foreach($data as $pe)
<tr>
    <td class="font-bold text-bps-dark">{{ $pe->kode_petugas }}</td>
    <td>{{ $pe->provinsi }}</td>
    <td>{{ $pe->kabupaten }}</td>
    <td class="font-medium">{{ $pe->nama_petugas }}</td>
    <td>{{ $pe->email }}</td>
    <td>{{ $pe->no_hp }}</td>
    <td>
        <span class="bg-green-500/10 text-green-600 text-[10px] font-bold px-3 py-1 rounded-full border border-green-200 uppercase">{{ $pe->status }}</span>
    </td>
</tr>
@endforeach

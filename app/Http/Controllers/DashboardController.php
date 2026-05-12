<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\PetugasLapangan;
use App\Models\PetugasEntry;
use App\Models\DataDssls;
use App\Models\DataDsrt;

class DashboardController extends Controller
{
    // Cache TTL in seconds (1 hour — petugas data is rarely updated)
    const PETUGAS_CACHE_TTL = 3600;

    public function index()
    {
        return view('dashboard.index');
    }

    /**
     * Lightweight JSON endpoint — returns petugas lists as JSON for
     * client-side dropdown population. No HTML rendering overhead.
     */
    public function petugasOptions()
    {
        return response()->json(Cache::remember('dashboard_petugas_options', self::PETUGAS_CACHE_TTL, function () {
            return [
                'ppl' => PetugasLapangan::query()
                    ->select('kode_petugas as kode', 'nama_petugas as nama')
                    ->where('jabatan', 'Pencacah (PPL)')
                    ->orderBy('nama_petugas')
                    ->get(),
                'pml' => PetugasLapangan::query()
                    ->select('kode_petugas as kode', 'nama_petugas as nama')
                    ->where('jabatan', 'Pengawas (PML)')
                    ->orderBy('nama_petugas')
                    ->get(),
                'entry' => PetugasEntry::query()
                    ->select('kode_petugas as kode', 'nama_petugas as nama')
                    ->orderBy('nama_petugas')
                    ->get(),
            ];
        }));
    }

    public function datatableLapangan(Request $request)
    {
        $columns = [
            0 => null,
            1 => 'kode_petugas',
            2 => 'provinsi',
            3 => 'kabupaten',
            4 => 'nama_petugas',
            5 => 'no_hp',
            6 => 'jabatan',
            7 => 'status',
        ];

        $recordsTotal = Cache::remember('count_petugas_lapangan', 300, fn() => PetugasLapangan::count());

        $filteredQuery = PetugasLapangan::query();
        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $filteredQuery->where(function ($query) use ($search) {
                $query->where('kode_petugas', 'like', "%{$search}%")
                    ->orWhere('provinsi', 'like', "%{$search}%")
                    ->orWhere('kabupaten', 'like', "%{$search}%")
                    ->orWhere('nama_petugas', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $search !== '' ? (clone $filteredQuery)->count() : $recordsTotal;
        $orderIndex = (int) $request->input('order.0.column', 7);
        $orderColumn = $columns[$orderIndex] ?? 'nama_petugas';
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', -1);
        if ($length !== -1) {
            $filteredQuery->skip($start)->take($length);
        }

        $data = $filteredQuery
            ->select(['kode_petugas', 'provinsi', 'kabupaten', 'nama_petugas', 'no_hp', 'jabatan', 'status'])
            ->orderBy($orderColumn, $orderDirection)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function datatableEntry(Request $request)
    {
        $columns = [
            0 => null,
            1 => 'kode_petugas',
            2 => 'provinsi',
            3 => 'kabupaten',
            4 => 'nama_petugas',
            5 => 'email',
            6 => 'no_hp',
            7 => 'status',
        ];

        $recordsTotal = Cache::remember('count_petugas_entry', 300, fn() => PetugasEntry::count());

        $filteredQuery = PetugasEntry::query();
        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $filteredQuery->where(function ($query) use ($search) {
                $query->where('kode_petugas', 'like', "%{$search}%")
                    ->orWhere('provinsi', 'like', "%{$search}%")
                    ->orWhere('kabupaten', 'like', "%{$search}%")
                    ->orWhere('nama_petugas', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $search !== '' ? (clone $filteredQuery)->count() : $recordsTotal;
        $orderIndex = (int) $request->input('order.0.column', 7);
        $orderColumn = $columns[$orderIndex] ?? 'nama_petugas';
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', -1);
        if ($length !== -1) {
            $filteredQuery->skip($start)->take($length);
        }

        $data = $filteredQuery
            ->select(['kode_petugas', 'provinsi', 'kabupaten', 'nama_petugas', 'email', 'no_hp', 'status'])
            ->orderBy($orderColumn, $orderDirection)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function datatableDssls(Request $request)
    {
        $columns = [
            0 => null,
            1 => null,
            2 => 'data_dssls.nama_kecamatan',
            3 => 'data_dssls.kode_sls',
            4 => 'data_dssls.ceklis_lap',
            5 => 'data_dssls.ceklis_sosial',
            6 => 'data_dssls.ceklis_ipds',
            7 => 'ppl.nama_petugas',
            8 => 'pml.nama_petugas',
            9 => 'entry.nama_petugas',
        ];

        $baseQuery = DataDssls::query()
            ->leftJoin('petugas_lapangans as ppl', 'data_dssls.petugas_ppl', '=', 'ppl.kode_petugas')
            ->leftJoin('petugas_lapangans as pml', 'data_dssls.petugas_pml', '=', 'pml.kode_petugas')
            ->leftJoin('petugas_entries as entry', 'data_dssls.petugas_entry', '=', 'entry.kode_petugas');

        $recordsTotal = Cache::remember('count_data_dssls', 120, fn() => DataDssls::count());
        $filteredQuery = clone $baseQuery;

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $filteredQuery->where(function ($query) use ($search) {
                $query->where('data_dssls.nama_kecamatan', 'like', "%{$search}%")
                    ->orWhere('data_dssls.nama_desa_kelurahan', 'like', "%{$search}%")
                    ->orWhere('data_dssls.kode_sls', 'like', "%{$search}%")
                    ->orWhere('data_dssls.nama_sls', 'like', "%{$search}%")
                    ->orWhere('data_dssls.nks', 'like', "%{$search}%")
                    ->orWhere('ppl.nama_petugas', 'like', "%{$search}%")
                    ->orWhere('pml.nama_petugas', 'like', "%{$search}%")
                    ->orWhere('entry.nama_petugas', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $search !== '' ? (clone $filteredQuery)->count('data_dssls.id') : $recordsTotal;
        $orderIndex = (int) $request->input('order.0.column', 2);
        $orderColumn = $columns[$orderIndex] ?? 'data_dssls.nama_kecamatan';
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        if ($orderColumn) {
            $filteredQuery->orderBy($orderColumn, $orderDirection);
        }

        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', -1);
        if ($length !== -1) {
            $filteredQuery->skip($start)->take($length);
        }

        $data = $filteredQuery
            ->select([
                'data_dssls.id',
                'data_dssls.nama_kecamatan',
                'data_dssls.nama_desa_kelurahan',
                'data_dssls.kode_sls',
                'data_dssls.nama_sls',
                'data_dssls.perkiraan_jumlah_keluarga',
                'data_dssls.sampel_seruti',
                'data_dssls.petugas_ppl',
                'data_dssls.petugas_pml',
                'data_dssls.petugas_entry',
                'data_dssls.ceklis_lap',
                'data_dssls.waktu_ceklis_lap',
                'data_dssls.ceklis_sosial',
                'data_dssls.waktu_ceklis_sosial',
                'data_dssls.ceklis_ipds',
                'data_dssls.waktu_ceklis_ipds',
                DB::raw('ppl.nama_petugas as petugas_ppl_nama'),
                DB::raw('pml.nama_petugas as petugas_pml_nama'),
                DB::raw('entry.nama_petugas as petugas_entry_nama'),
            ])
            ->get()
            ->map(fn($row) => $this->formatDsslsRow($row));

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function datatableDsrt(Request $request)
    {
        $columns = [
            0 => null,
            1 => null,
            2 => 'data_dsrts.kec',
            3 => 'data_dsrts.nmslsm',
            4 => 'data_dsrts.r503',
            5 => 'data_dsrts.ceklis_lap',
            6 => 'data_dsrts.ceklis_sosial',
            7 => 'data_dsrts.ceklis_ipds',
            8 => 'ppl.nama_petugas',
            9 => 'pml.nama_petugas',
            10 => 'susenas.nama_petugas',
            11 => 'seruti.nama_petugas',
        ];

        $baseQuery = DataDsrt::query()
            ->leftJoin('petugas_lapangans as ppl', 'data_dsrts.petugas_ppl', '=', 'ppl.kode_petugas')
            ->leftJoin('petugas_lapangans as pml', 'data_dsrts.petugas_pml', '=', 'pml.kode_petugas')
            ->leftJoin('petugas_entries as susenas', 'data_dsrts.petugas_susenas', '=', 'susenas.kode_petugas')
            ->leftJoin('petugas_entries as seruti', 'data_dsrts.petugas_seruti', '=', 'seruti.kode_petugas');

        $recordsTotal = Cache::remember('count_data_dsrt', 120, fn() => DataDsrt::count());
        $filteredQuery = clone $baseQuery;

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $filteredQuery->where(function ($query) use ($search) {
                $query->where('data_dsrts.kec', 'like', "%{$search}%")
                    ->orWhere('data_dsrts.desa', 'like', "%{$search}%")
                    ->orWhere('data_dsrts.nmkec', 'like', "%{$search}%")
                    ->orWhere('data_dsrts.nmdesa', 'like', "%{$search}%")
                    ->orWhere('data_dsrts.nmslsm', 'like', "%{$search}%")
                    ->orWhere('data_dsrts.nks_sak22', 'like', "%{$search}%")
                    ->orWhere('data_dsrts.r503', 'like', "%{$search}%")
                    ->orWhere('ppl.nama_petugas', 'like', "%{$search}%")
                    ->orWhere('pml.nama_petugas', 'like', "%{$search}%")
                    ->orWhere('susenas.nama_petugas', 'like', "%{$search}%")
                    ->orWhere('seruti.nama_petugas', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $search !== '' ? (clone $filteredQuery)->count('data_dsrts.id') : $recordsTotal;
        $orderIndex = (int) $request->input('order.0.column', 2);
        $orderColumn = $columns[$orderIndex] ?? 'data_dsrts.kec';
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        if ($orderColumn) {
            $filteredQuery->orderBy($orderColumn, $orderDirection);
        }

        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', -1);
        if ($length !== -1) {
            $filteredQuery->skip($start)->take($length);
        }

        $data = $filteredQuery
            ->select([
                'data_dsrts.id',
                'data_dsrts.kec',
                'data_dsrts.desa',
                'data_dsrts.nmkec',
                'data_dsrts.nmdesa',
                'data_dsrts.nks_sak22',
                'data_dsrts.nmslsm',
                'data_dsrts.r503',
                'data_dsrts.r503b',
                'data_dsrts.petugas_ppl',
                'data_dsrts.petugas_pml',
                'data_dsrts.petugas_susenas',
                'data_dsrts.petugas_seruti',
                'data_dsrts.ceklis_lap',
                'data_dsrts.waktu_ceklis_lap',
                'data_dsrts.ceklis_sosial',
                'data_dsrts.waktu_ceklis_sosial',
                'data_dsrts.ceklis_ipds',
                'data_dsrts.waktu_ceklis_ipds',
                DB::raw('ppl.nama_petugas as petugas_ppl_nama'),
                DB::raw('pml.nama_petugas as petugas_pml_nama'),
                DB::raw('susenas.nama_petugas as petugas_susenas_nama'),
                DB::raw('seruti.nama_petugas as petugas_seruti_nama'),
            ])
            ->get()
            ->map(fn($row) => $this->formatDsrtRow($row));

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    private function formatDsslsRow(DataDssls $row): array
    {
        return [
            'id' => $row->id,
            'nama_kecamatan' => $row->nama_kecamatan,
            'nama_desa_kelurahan' => $row->nama_desa_kelurahan,
            'kode_sls' => $row->kode_sls,
            'nama_sls' => $row->nama_sls,
            'perkiraan_jumlah_keluarga' => $row->perkiraan_jumlah_keluarga,
            'sampel_seruti' => $row->sampel_seruti,
            'petugas_ppl' => $row->petugas_ppl,
            'petugas_ppl_nama' => $row->petugas_ppl_nama,
            'petugas_pml' => $row->petugas_pml,
            'petugas_pml_nama' => $row->petugas_pml_nama,
            'petugas_entry' => $row->petugas_entry,
            'petugas_entry_nama' => $row->petugas_entry_nama,
            'ceklis_lap' => (bool) $row->ceklis_lap,
            'waktu_ceklis_lap' => $this->formatDashboardDate($row->waktu_ceklis_lap),
            'ceklis_sosial' => (bool) $row->ceklis_sosial,
            'waktu_ceklis_sosial' => $this->formatDashboardDate($row->waktu_ceklis_sosial),
            'ceklis_ipds' => (bool) $row->ceklis_ipds,
            'waktu_ceklis_ipds' => $this->formatDashboardDate($row->waktu_ceklis_ipds),
        ];
    }

    private function formatDsrtRow(DataDsrt $row): array
    {
        return [
            'id' => $row->id,
            'kec' => $row->kec,
            'desa' => $row->desa,
            'nmkec' => $row->nmkec,
            'nmdesa' => $row->nmdesa,
            'nks_sak22' => $row->nks_sak22,
            'nmslsm' => $row->nmslsm,
            'r503' => $row->r503,
            'r503b' => $row->r503b,
            'petugas_ppl' => $row->petugas_ppl,
            'petugas_ppl_nama' => $row->petugas_ppl_nama,
            'petugas_pml' => $row->petugas_pml,
            'petugas_pml_nama' => $row->petugas_pml_nama,
            'petugas_susenas' => $row->petugas_susenas,
            'petugas_susenas_nama' => $row->petugas_susenas_nama,
            'petugas_seruti' => $row->petugas_seruti,
            'petugas_seruti_nama' => $row->petugas_seruti_nama,
            'ceklis_lap' => (bool) $row->ceklis_lap,
            'waktu_ceklis_lap' => $this->formatDashboardDate($row->waktu_ceklis_lap),
            'ceklis_sosial' => (bool) $row->ceklis_sosial,
            'waktu_ceklis_sosial' => $this->formatDashboardDate($row->waktu_ceklis_sosial),
            'ceklis_ipds' => (bool) $row->ceklis_ipds,
            'waktu_ceklis_ipds' => $this->formatDashboardDate($row->waktu_ceklis_ipds),
        ];
    }

    private function formatDashboardDate($value): ?string
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d/m H:i') : null;
    }
}

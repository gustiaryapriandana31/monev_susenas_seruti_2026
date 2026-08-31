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
        return response()->json([
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
        ]);
    }

    public function summaryData(Request $request)
    {
        $kecamatan = $request->input('kecamatan');
        $desa = $request->input('desa');

        // Dynamic list of Kecamatan and Desa (always calculated)
        $kecDesaList = DataDsrt::select('nmkec', 'nmdesa')
            ->distinct()
            ->orderBy('nmkec')
            ->orderBy('nmdesa')
            ->get()
            ->groupBy('nmkec')
            ->map(function($items) {
                return $items->pluck('nmdesa')->unique()->values();
            });

        $totalPpl = PetugasLapangan::where('jabatan', 'Pencacah (PPL)')->count();
        $totalPml = PetugasLapangan::where('jabatan', 'Pengawas (PML)')->count();
        $totalEntry = PetugasEntry::count();

        // Set up queries
        $dsslsQuery = DataDssls::query();
        $dsrtQuery = DataDsrt::query();

        if ($kecamatan) {
            $dsslsQuery->whereRaw('TRIM(nama_kecamatan) = ?', [$kecamatan]);
            $dsrtQuery->where('nmkec', $kecamatan);
        }
        if ($desa) {
            $dsslsQuery->whereRaw('TRIM(nama_desa_kelurahan) = ?', [$desa]);
            $dsrtQuery->where('nmdesa', $desa);
        }

        // Target counts
        $dsslsTotal = (clone $dsslsQuery)->count();
        $dsslsLap = (clone $dsslsQuery)->where('ceklis_lap', true)->count();
        $dsslsSosial = (clone $dsslsQuery)->where('ceklis_sosial', true)->count();
        $dsslsIpds = (clone $dsslsQuery)->where('ceklis_ipds', true)->count();

        $dsrtTotal = (clone $dsrtQuery)->count();
        $dsrtLap = (clone $dsrtQuery)->where('ceklis_lap', true)->count();
        $dsrtSosial = (clone $dsrtQuery)->where('ceklis_sosial', true)->count();
        $dsrtIpds = (clone $dsrtQuery)->where('ceklis_ipds', true)->count();
        $dsrtPemeriksaan = (clone $dsrtQuery)->where('ceklis_pemeriksaan', true)->count();

        // Overall progress: fully completed (all checklists = 1)
        $dsslsFullyCompleted = (clone $dsslsQuery)
            ->where('ceklis_lap', true)
            ->where('ceklis_sosial', true)
            ->where('ceklis_ipds', true)
            ->count();

        $dsrtFullyCompleted = (clone $dsrtQuery)
            ->where('ceklis_lap', true)
            ->where('ceklis_sosial', true)
            ->where('ceklis_ipds', true)
            ->where('ceklis_pemeriksaan', true)
            ->count();

        $dsslsProgress = $dsslsTotal > 0 ? round(($dsslsFullyCompleted / $dsslsTotal) * 100, 1) : 0;
        $dsrtProgress = $dsrtTotal > 0 ? round(($dsrtFullyCompleted / $dsrtTotal) * 100, 1) : 0;

        // Sebaran PML, PPL, Entry di DSSLS
        $pplDssls = (clone $dsslsQuery)->select('petugas_ppl', DB::raw('count(*) as total'))
            ->whereNotNull('petugas_ppl')->where('petugas_ppl', '!=', '')
            ->groupBy('petugas_ppl')->orderByDesc('total')->get()
            ->map(function ($item) {
                $p = PetugasLapangan::where('kode_petugas', $item->petugas_ppl)->first();
                return ['nama' => $p ? $p->nama_petugas : $item->petugas_ppl, 'total' => $item->total];
            });

        $pmlDssls = (clone $dsslsQuery)->select('petugas_pml', DB::raw('count(*) as total'))
            ->whereNotNull('petugas_pml')->where('petugas_pml', '!=', '')
            ->groupBy('petugas_pml')->orderByDesc('total')->get()
            ->map(function ($item) {
                $p = PetugasLapangan::where('kode_petugas', $item->petugas_pml)->first();
                return ['nama' => $p ? $p->nama_petugas : $item->petugas_pml, 'total' => $item->total];
            });

        $entryDssls = (clone $dsslsQuery)->select('petugas_entry', DB::raw('count(*) as total'))
            ->whereNotNull('petugas_entry')->where('petugas_entry', '!=', '')
            ->groupBy('petugas_entry')->orderByDesc('total')->get()
            ->map(function ($item) {
                $p = PetugasEntry::where('kode_petugas', $item->petugas_entry)->first();
                return ['nama' => $p ? $p->nama_petugas : $item->petugas_entry, 'total' => $item->total];
            });

        // Sebaran PML, PPL, SUSENAS, SERUTI di DSRT
        $pplDsrt = (clone $dsrtQuery)->select('petugas_ppl', DB::raw('count(*) as total'))
            ->whereNotNull('petugas_ppl')->where('petugas_ppl', '!=', '')
            ->groupBy('petugas_ppl')->orderByDesc('total')->get()
            ->map(function ($item) {
                $p = PetugasLapangan::where('kode_petugas', $item->petugas_ppl)->first();
                return ['nama' => $p ? $p->nama_petugas : $item->petugas_ppl, 'total' => $item->total];
            });

        $pmlDsrt = (clone $dsrtQuery)->select('petugas_pml', DB::raw('count(*) as total'))
            ->whereNotNull('petugas_pml')->where('petugas_pml', '!=', '')
            ->groupBy('petugas_pml')->orderByDesc('total')->get()
            ->map(function ($item) {
                $p = PetugasLapangan::where('kode_petugas', $item->petugas_pml)->first();
                return ['nama' => $p ? $p->nama_petugas : $item->petugas_pml, 'total' => $item->total];
            });

        $susenasDsrt = (clone $dsrtQuery)->select('petugas_susenas', DB::raw('count(*) as total'))
            ->whereNotNull('petugas_susenas')->where('petugas_susenas', '!=', '')
            ->groupBy('petugas_susenas')->orderByDesc('total')->get()
            ->map(function ($item) {
                $p = PetugasEntry::where('kode_petugas', $item->petugas_susenas)->first();
                return ['nama' => $p ? $p->nama_petugas : $item->petugas_susenas, 'total' => $item->total];
            });

        $serutiDsrt = (clone $dsrtQuery)->select('petugas_seruti', DB::raw('count(*) as total'))
            ->whereNotNull('petugas_seruti')->where('petugas_seruti', '!=', '')
            ->groupBy('petugas_seruti')->orderByDesc('total')->get()
            ->map(function ($item) {
                $p = PetugasEntry::where('kode_petugas', $item->petugas_seruti)->first();
                return ['nama' => $p ? $p->nama_petugas : $item->petugas_seruti, 'total' => $item->total];
            });

        // R203 KOR & KP Status counts
        $r203Kor = (clone $dsrtQuery)->select('r203_kor', DB::raw('count(*) as total'))->groupBy('r203_kor')->get()
            ->map(function ($item) {
                $status = $item->r203_kor;
                $label = $status ? $status->label() : 'Belum Terisi';
                return ['label' => $label, 'total' => $item->total];
            });

        $r203Kp = (clone $dsrtQuery)->select('r203_kp', DB::raw('count(*) as total'))->groupBy('r203_kp')->get()
            ->map(function ($item) {
                $status = $item->r203_kp;
                $label = $status ? $status->label() : 'Belum Terisi';
                return ['label' => $label, 'total' => $item->total];
            });

        // Catatan KOR & KP counts
        $catatanKor = (clone $dsrtQuery)->select('blok_catatan_kor', DB::raw('count(*) as total'))->groupBy('blok_catatan_kor')->get()
            ->map(function ($item) {
                $label = $item->blok_catatan_kor ? 'Ada Catatan (Ya)' : 'Tidak Ada Catatan (Tidak)';
                return ['label' => $label, 'total' => $item->total];
            });

        $catatanKp = (clone $dsrtQuery)->select('blok_catatan_kp', DB::raw('count(*) as total'))->groupBy('blok_catatan_kp')->get()
            ->map(function ($item) {
                $label = $item->blok_catatan_kp ? 'Ada Catatan (Ya)' : 'Tidak Ada Catatan (Tidak)';
                return ['label' => $label, 'total' => $item->total];
            });

        // Rekap Penugasan Petugas Entry
        $dsslsCountsQuery = DataDssls::select('petugas_entry', DB::raw('count(*) as count'))
            ->whereNotNull('petugas_entry')
            ->groupBy('petugas_entry');
        if ($kecamatan) {
            $dsslsCountsQuery->whereRaw('TRIM(nama_kecamatan) = ?', [$kecamatan]);
        }
        if ($desa) {
            $dsslsCountsQuery->whereRaw('TRIM(nama_desa_kelurahan) = ?', [$desa]);
        }
        $dsslsCounts = $dsslsCountsQuery->pluck('count', 'petugas_entry')->toArray();

        $susenasCountsQuery = DataDsrt::select('petugas_susenas', DB::raw('count(*) as count'))
            ->whereNotNull('petugas_susenas')
            ->groupBy('petugas_susenas');
        if ($kecamatan) {
            $susenasCountsQuery->where('nmkec', $kecamatan);
        }
        if ($desa) {
            $susenasCountsQuery->where('nmdesa', $desa);
        }
        $susenasCounts = $susenasCountsQuery->pluck('count', 'petugas_susenas')->toArray();

        $serutiCountsQuery = DataDsrt::select('petugas_seruti', DB::raw('count(*) as count'))
            ->whereNotNull('petugas_seruti')
            ->groupBy('petugas_seruti');
        if ($kecamatan) {
            $serutiCountsQuery->where('nmkec', $kecamatan);
        }
        if ($desa) {
            $serutiCountsQuery->where('nmdesa', $desa);
        }
        $serutiCounts = $serutiCountsQuery->pluck('count', 'petugas_seruti')->toArray();

        $rekapEntry = PetugasEntry::select('kode_petugas', 'nama_petugas')
            ->get()
            ->map(function ($p) use ($dsslsCounts, $susenasCounts, $serutiCounts) {
                $pemutakhiran = $dsslsCounts[$p->kode_petugas] ?? 0;
                $susenas = $susenasCounts[$p->kode_petugas] ?? 0;
                $seruti = $serutiCounts[$p->kode_petugas] ?? 0;
                return [
                    'nama' => $p->nama_petugas,
                    'kode' => $p->kode_petugas,
                    'pemutakhiran' => $pemutakhiran,
                    'susenas' => $susenas,
                    'seruti' => $seruti,
                    'total' => $pemutakhiran + $susenas + $seruti,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->all();

        $data = [
            'petugas' => [
                'ppl' => $totalPpl,
                'pml' => $totalPml,
                'entry' => $totalEntry,
                'total' => $totalPpl + $totalPml + $totalEntry
            ],
            'dssls' => [
                'total' => $dsslsTotal,
                'lap' => $dsslsLap,
                'sosial' => $dsslsSosial,
                'ipds' => $dsslsIpds,
                'progress' => $dsslsProgress,
                'completed' => $dsslsFullyCompleted,
                'sebaran' => [
                    'ppl' => $pplDssls,
                    'pml' => $pmlDssls,
                    'entry' => $entryDssls
                ]
            ],
            'dsrt' => [
                'total' => $dsrtTotal,
                'lap' => $dsrtLap,
                'sosial' => $dsrtSosial,
                'ipds' => $dsrtIpds,
                'pemeriksaan' => $dsrtPemeriksaan,
                'progress' => $dsrtProgress,
                'completed' => $dsrtFullyCompleted,
                'sebaran' => [
                    'ppl' => $pplDsrt,
                    'pml' => $pmlDsrt,
                    'susenas' => $susenasDsrt,
                    'seruti' => $serutiDsrt
                ],
                'r203_kor' => $r203Kor,
                'r203_kp' => $r203Kp,
                'catatan_kor' => $catatanKor,
                'catatan_kp' => $catatanKp
            ],
            'rekap_entry' => $rekapEntry,
            'kec_desa' => $kecDesaList
        ];

        return response()->json($data);
    }

    public function exportRekap(Request $request)
    {
        $kecamatan = $request->input('kecamatan');
        $desa = $request->input('desa');

        // Rekap Penugasan Petugas Entry
        $dsslsCountsQuery = DataDssls::select('petugas_entry', DB::raw('count(*) as count'))
            ->whereNotNull('petugas_entry')
            ->groupBy('petugas_entry');
        if ($kecamatan) {
            $dsslsCountsQuery->whereRaw('TRIM(nama_kecamatan) = ?', [$kecamatan]);
        }
        if ($desa) {
            $dsslsCountsQuery->whereRaw('TRIM(nama_desa_kelurahan) = ?', [$desa]);
        }
        $dsslsCounts = $dsslsCountsQuery->pluck('count', 'petugas_entry')->toArray();

        $susenasCountsQuery = DataDsrt::select('petugas_susenas', DB::raw('count(*) as count'))
            ->whereNotNull('petugas_susenas')
            ->groupBy('petugas_susenas');
        if ($kecamatan) {
            $susenasCountsQuery->where('nmkec', $kecamatan);
        }
        if ($desa) {
            $susenasCountsQuery->where('nmdesa', $desa);
        }
        $susenasCounts = $susenasCountsQuery->pluck('count', 'petugas_susenas')->toArray();

        $serutiCountsQuery = DataDsrt::select('petugas_seruti', DB::raw('count(*) as count'))
            ->whereNotNull('petugas_seruti')
            ->groupBy('petugas_seruti');
        if ($kecamatan) {
            $serutiCountsQuery->where('nmkec', $kecamatan);
        }
        if ($desa) {
            $serutiCountsQuery->where('nmdesa', $desa);
        }
        $serutiCounts = $serutiCountsQuery->pluck('count', 'petugas_seruti')->toArray();

        $rekapEntry = PetugasEntry::select('kode_petugas', 'nama_petugas')
            ->get()
            ->map(function ($p) use ($dsslsCounts, $susenasCounts, $serutiCounts) {
                $pemutakhiran = $dsslsCounts[$p->kode_petugas] ?? 0;
                $susenas = $susenasCounts[$p->kode_petugas] ?? 0;
                $seruti = $serutiCounts[$p->kode_petugas] ?? 0;
                return [
                    'nama' => $p->nama_petugas,
                    'kode' => $p->kode_petugas,
                    'pemutakhiran' => $pemutakhiran,
                    'susenas' => $susenas,
                    'seruti' => $seruti,
                    'total' => $pemutakhiran + $susenas + $seruti,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->all();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RekapPetugasExport($rekapEntry),
            'rekap_penugasan_petugas_entry.xlsx'
        );
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

        $recordsTotal = PetugasLapangan::count();

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
            ->select(['id', 'kode_petugas', 'provinsi', 'kabupaten', 'nama_petugas', 'no_hp', 'jabatan', 'status'])
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

        $recordsTotal = PetugasEntry::count();

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
            ->select(['id', 'kode_petugas', 'provinsi', 'kabupaten', 'nama_petugas', 'email', 'no_hp', 'status'])
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

        $recordsTotal = DataDssls::count();
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
                'data_dssls.jumlah_keluarga_awal',
                'data_dssls.jumlah_keluarga_hasil_updating',
                'data_dssls.jumlah_rumah_tangga_hasil_updating',
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
            8 => 'data_dsrts.ceklis_pemeriksaan',
            9 => 'ppl.nama_petugas',
            10 => 'pml.nama_petugas',
            11 => 'susenas.nama_petugas',
            12 => 'seruti.nama_petugas',
        ];

        $baseQuery = DataDsrt::query()
            ->leftJoin('petugas_lapangans as ppl', 'data_dsrts.petugas_ppl', '=', 'ppl.kode_petugas')
            ->leftJoin('petugas_lapangans as pml', 'data_dsrts.petugas_pml', '=', 'pml.kode_petugas')
            ->leftJoin('petugas_entries as susenas', 'data_dsrts.petugas_susenas', '=', 'susenas.kode_petugas')
            ->leftJoin('petugas_entries as seruti', 'data_dsrts.petugas_seruti', '=', 'seruti.kode_petugas');

        $recordsTotal = DataDsrt::count();
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
                'data_dsrts.ceklis_pemeriksaan',
                'data_dsrts.waktu_ceklis_pemeriksaan',
                'data_dsrts.r203_kor',
                'data_dsrts.r203_kp',
                'data_dsrts.r301_jumlah_art',
                'data_dsrts.r304_vsen26kp',
                'data_dsrts.r305_vsen26kp',
                'data_dsrts.blok_catatan_kor',
                'data_dsrts.blok_catatan_kp',
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
            'jumlah_keluarga_awal' => $row->jumlah_keluarga_awal,
            'jumlah_keluarga_hasil_updating' => $row->jumlah_keluarga_hasil_updating,
            'jumlah_rumah_tangga_hasil_updating' => $row->jumlah_rumah_tangga_hasil_updating,
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
            'ceklis_pemeriksaan' => (bool) $row->ceklis_pemeriksaan,
            'waktu_ceklis_pemeriksaan' => $this->formatDashboardDate($row->waktu_ceklis_pemeriksaan),
            'r203_kor' => $row->r203_kor?->value,
            'r203_kor_label' => $row->r203_kor?->label(),
            'r203_kp' => $row->r203_kp?->value,
            'r203_kp_label' => $row->r203_kp?->label(),
            'r301_jumlah_art' => $row->r301_jumlah_art,
            'r304_vsen26kp' => $row->r304_vsen26kp,
            'r305_vsen26kp' => $row->r305_vsen26kp,
            'blok_catatan_kor' => $row->blok_catatan_kor,
            'blok_catatan_kp' => $row->blok_catatan_kp,
        ];
    }

    private function formatDashboardDate($value): ?string
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d/m H:i') : null;
    }
}

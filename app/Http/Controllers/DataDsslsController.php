<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataDsslsImport;
use App\Exports\DataDsslsExport;
use App\Exports\DataDsslsOriExport;
use App\Models\DataDssls;
use Carbon\Carbon;

class DataDsslsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user->isAdminIpds()) {
                $route = $request->route()->getActionMethod();
                if ($route === 'toggleCeklis') {
                    if ($request->field !== 'ceklis_ipds') {
                        return $request->ajax() 
                            ? response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403)
                            : abort(403, 'Akses ditolak.');
                    }
                } elseif ($route === 'updateInline') {
                    if ($request->field !== 'petugas_entry') {
                        return $request->ajax() 
                            ? response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403)
                            : abort(403, 'Akses ditolak.');
                    }
                } else {
                    // Block import, modal update, deleteBulk, deleteAll for IPDS
                    if (in_array($route, ['import', 'update', 'deleteBulk', 'deleteAll'])) {
                        return $request->ajax() 
                            ? response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403)
                            : abort(403, 'Akses ditolak.');
                    }
                }
            }
            return $next($request);
        });
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new DataDsslsImport, $request->file('file_excel'));

        Cache::forget('count_data_dssls');

        return back()->with('success', 'Data DSSLS Berhasil Diimport!')->with('active_tab', 'dssls');
    }

    public function toggleCeklis(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:data_dssls,id',
            'field' => 'required|in:ceklis_lap,ceklis_sosial,ceklis_ipds',
            'state' => 'required|in:0,1'
        ]);

        $user = auth()->user();
        $field = $request->field;

        // Backend guard: adminsosial cannot toggle ceklis_ipds
        if ($user->isAdminSosial() && $field === 'ceklis_ipds') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Admin Sosial tidak diizinkan mengubah Ceklis IPDS.'], 403);
        }

        // Backend guard: adminipds can only toggle ceklis_ipds
        if ($user->isAdminIpds() && $field !== 'ceklis_ipds') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. IPDS hanya diizinkan mengubah Ceklis IPDS.'], 403);
        }

        $item       = DataDssls::findOrFail($request->id);
        $waktuField = 'waktu_' . $field;

        $state = (int) $request->state;
        $item->$field = ($state === 1);
        $timestamp    = null;

        if ($state === 1) {
            $item->$waktuField = Carbon::now();
            $timestamp         = $item->$waktuField->format('d/m/y H:i');
        } else {
            $item->$waktuField = null;
        }

        $item->save();

        // Invalidate cache so next table load is fresh
        Cache::forget('count_data_dssls');

        return response()->json(['success' => true, 'timestamp' => $timestamp]);
    }

    public function export()
    {
        return Excel::download(new DataDsslsExport, 'Export Data DSSLS Pemutakhiran Sosial.xlsx');
    }

    public function exportOri()
    {
        return Excel::download(new DataDsslsOriExport, 'Export Data DSSLS Full.xlsx');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'                                  => 'required|exists:data_dssls,id',
            'perkiraan_jumlah_keluarga'          => 'nullable|integer',
            'jumlah_keluarga_awal'                => 'nullable|integer',
            'jumlah_keluarga_hasil_updating'      => 'nullable|integer',
            'jumlah_rumah_tangga_hasil_updating' => 'nullable|integer',
            'sampel_seruti'                       => 'nullable|integer',
            'petugas_ppl'                         => 'nullable|string',
            'petugas_pml'                         => 'nullable|string',
            'petugas_entry'                       => 'nullable|string',
        ]);

        $item                                     = DataDssls::find($request->id);
        $item->perkiraan_jumlah_keluarga          = $request->perkiraan_jumlah_keluarga;
        $item->jumlah_keluarga_awal                = $request->jumlah_keluarga_awal;
        $item->jumlah_keluarga_hasil_updating      = $request->jumlah_keluarga_hasil_updating;
        $item->jumlah_rumah_tangga_hasil_updating = $request->jumlah_rumah_tangga_hasil_updating;
        $item->sampel_seruti                      = $request->sampel_seruti;
        $item->petugas_ppl                        = $request->petugas_ppl;
        $item->petugas_pml                        = $request->petugas_pml;
        $item->petugas_entry                      = $request->petugas_entry;
        $item->save();

        Cache::forget('count_data_dssls');

        return back()->with('success', 'Data DSSLS berhasil diperbarui.')->with('active_tab', 'dssls');
    }

    public function updateInline(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:data_dssls,id',
            'field' => 'required|in:petugas_ppl,petugas_pml,petugas_entry,jumlah_keluarga_awal,jumlah_keluarga_hasil_updating,jumlah_rumah_tangga_hasil_updating',
            'value' => 'nullable'
        ]);

        $user = auth()->user();
        $field = $request->field;

        // Backend guard: adminsosial cannot update petugas_entry
        if ($user->isAdminSosial() && $field === 'petugas_entry') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Admin Sosial tidak diizinkan mengubah petugas entry.'], 403);
        }

        // Backend guard: adminipds can only update petugas_entry
        if ($user->isAdminIpds() && $field !== 'petugas_entry') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $item         = DataDssls::find($request->id);
        $value        = $request->value;

        if (in_array($field, ['jumlah_keluarga_awal', 'jumlah_keluarga_hasil_updating', 'jumlah_rumah_tangga_hasil_updating'])) {
            $value = ($value === '' || $value === null) ? null : (int) $value;
        }

        $item->$field = $value;
        $item->save();

        Cache::forget('count_data_dssls');

        return response()->json(['success' => true, 'message' => 'Data berhasil diupdate']);
    }

    public function deleteBulk(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:data_dssls,id'
        ]);

        DataDssls::whereIn('id', $request->ids)->delete();

        Cache::forget('count_data_dssls');

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' data berhasil dihapus'
        ]);
    }

    public function deleteAll()
    {
        DataDssls::truncate();

        Cache::forget('count_data_dssls');

        return response()->json(['success' => true, 'message' => 'Seluruh data berhasil dihapus']);
    }
}

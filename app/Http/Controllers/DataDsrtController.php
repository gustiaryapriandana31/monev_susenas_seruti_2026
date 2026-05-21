<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataDsrtImport;
use App\Exports\DataDsrtIPDSExport;
use App\Exports\DataDsrtSosialExport;
use App\Exports\DataDsrtSosialKabExport;
use App\Exports\DataDsrtLapanganExport;
use App\Exports\DataDsrtPemeriksaanExport;
use App\Models\DataDsrt;
use Carbon\Carbon;

class DataDsrtController extends Controller
{
    public function import(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang dapat mengimpor data DSRT.');
        }

        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new DataDsrtImport, $request->file('file_excel'));

        Cache::forget('count_data_dsrt');

        return back()->with('success', 'Data DSRT Berhasil Diimport!')->with('active_tab', 'dsrt');
    }

    public function toggleCeklis(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:data_dsrts,id',
            'field' => 'required|in:ceklis_lap,ceklis_sosial,ceklis_ipds,ceklis_pemeriksaan',
            'state' => 'required|in:0,1'
        ]);

        $user = auth()->user();
        $field = $request->field;

        if ($user->isAdminIpds() && $field !== 'ceklis_ipds') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. IPDS hanya diizinkan mengubah Ceklis IPDS.'], 403);
        }
        if ($user->isAdminSosial() && $field === 'ceklis_ipds') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Admin Sosial tidak diizinkan mengubah Ceklis IPDS.'], 403);
        }

        $item       = DataDsrt::findOrFail($request->id);
        $field      = $request->field;
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
        Cache::forget('count_data_dsrt');

        return response()->json(['success' => true, 'timestamp' => $timestamp]);
    }

    public function exportIPDS()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdminIpds()) {
            abort(403, 'Akses ditolak.');
        }
        return Excel::download(new DataDsrtIPDSExport, 'Export Data DSRT untuk IPDS.xlsx');
    }

    public function exportSosial()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdminSosial()) {
            abort(403, 'Akses ditolak.');
        }
        return Excel::download(new DataDsrtSosialExport, 'Export Data DSRT Sosial Penerimaan oleh Kabupaten.xlsx');
    }

    public function exportSosialKab()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdminSosial()) {
            abort(403, 'Akses ditolak.');
        }
        return Excel::download(new DataDsrtSosialKabExport, 'Export Data DSRT Sosial Pengiriman ke Kabupaten.xlsx');
    }

    public function exportLapangan()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdminSosial()) {
            abort(403, 'Akses ditolak.');
        }
        return Excel::download(new DataDsrtLapanganExport, 'Export Data DSRT untuk Lapangan.xlsx');
    }

    public function exportPemeriksaan()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdminSosial()) {
            abort(403, 'Akses ditolak.');
        }
        return Excel::download(new DataDsrtPemeriksaanExport, 'Export Data DSRT untuk Pemeriksaan.xlsx');
    }

    public function update(Request $request)
    {
        if (auth()->user()->isAdminIpds()) {
            abort(403, 'Akses ditolak. IPDS tidak diizinkan mengubah field DSRT.');
        }

        $request->validate([
            'id'              => 'required|exists:data_dsrts,id',
            'r503'            => 'nullable|string',
            'r503b'           => 'nullable|string',
            'petugas_ppl'     => 'nullable|string',
            'petugas_pml'     => 'nullable|string',
            'petugas_susenas' => 'nullable|string',
            'petugas_seruti'  => 'nullable|string',
        ]);

        $item                  = DataDsrt::find($request->id);
        $item->r503            = $request->r503;
        $item->r503b           = $request->r503b;
        $item->petugas_ppl     = $request->petugas_ppl;
        $item->petugas_pml     = $request->petugas_pml;
        $item->petugas_susenas = $request->petugas_susenas;
        $item->petugas_seruti  = $request->petugas_seruti;
        $item->save();

        Cache::forget('count_data_dsrt');

        return back()->with('success', 'Data DSRT berhasil diperbarui.')->with('active_tab', 'dsrt');
    }

    public function updateInline(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:data_dsrts,id',
            'field' => 'required|in:petugas_ppl,petugas_pml,petugas_susenas,petugas_seruti,r203_kor,r203_kp,r301_jumlah_art,r304_vsen26kp,r305_vsen26kp,blok_catatan_kor,blok_catatan_kp',
            'value' => 'nullable'
        ]);

        $user = auth()->user();
        $field = $request->field;

        // Backend guard: adminipds can only update petugas_susenas and petugas_seruti
        if ($user->isAdminIpds()) {
            if (!in_array($field, ['petugas_susenas', 'petugas_seruti'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
        }

        // Backend guard: adminsosial cannot update petugas_susenas and petugas_seruti
        if ($user->isAdminSosial()) {
            if (in_array($field, ['petugas_susenas', 'petugas_seruti'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
        }

        $item         = DataDsrt::find($request->id);
        $value        = $request->value;

        if (in_array($field, ['blok_catatan_kor', 'blok_catatan_kp'])) {
            if ($value === '' || $value === null) {
                $value = null;
            } else {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
        } elseif (in_array($field, ['r304_vsen26kp', 'r305_vsen26kp'])) {
            $value = ($value === '' || $value === null) ? null : (int) $value;
        }

        $item->$field = $value;
        $item->save();

        Cache::forget('count_data_dsrt');

        return response()->json(['success' => true, 'message' => 'Data berhasil diupdate']);
    }

    public function deleteBulk(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya Super Admin yang dapat menghapus data DSRT.'], 403);
        }

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:data_dsrts,id'
        ]);

        DataDsrt::whereIn('id', $request->ids)->delete();

        Cache::forget('count_data_dsrt');

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' data berhasil dihapus'
        ]);
    }

    public function deleteAll()
    {
        if (!auth()->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya Super Admin yang dapat me-reset data DSRT.'], 403);
        }

        DataDsrt::truncate();

        Cache::forget('count_data_dsrt');

        return response()->json(['success' => true, 'message' => 'Seluruh data berhasil dihapus']);
    }
}

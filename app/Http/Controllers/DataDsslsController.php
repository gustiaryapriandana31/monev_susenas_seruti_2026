<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataDsslsImport;
use App\Exports\DataDsslsExport;
use App\Models\DataDssls;
use Carbon\Carbon;

class DataDsslsController extends Controller
{
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

        $item       = DataDssls::find($request->id);
        $field      = $request->field;
        $waktuField = 'waktu_' . $field;

        $item->$field = $request->state == 1;
        $timestamp    = null;

        if ($request->state == 1) {
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
        return Excel::download(new DataDsslsExport, 'data_dssls_export_' . date('Ymd_His') . '.xlsx');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'                       => 'required|exists:data_dssls,id',
            'perkiraan_jumlah_keluarga' => 'nullable|integer',
            'sampel_seruti'            => 'nullable|integer',
            'petugas_ppl'              => 'nullable|string',
            'petugas_pml'              => 'nullable|string',
            'petugas_entry'            => 'nullable|string',
        ]);

        $item                           = DataDssls::find($request->id);
        $item->perkiraan_jumlah_keluarga = $request->perkiraan_jumlah_keluarga;
        $item->sampel_seruti            = $request->sampel_seruti;
        $item->petugas_ppl              = $request->petugas_ppl;
        $item->petugas_pml              = $request->petugas_pml;
        $item->petugas_entry            = $request->petugas_entry;
        $item->save();

        Cache::forget('count_data_dssls');

        return back()->with('success', 'Data DSSLS berhasil diperbarui.')->with('active_tab', 'dssls');
    }

    public function updateInline(Request $request)
    {
        $request->validate([
            'id'    => 'required|exists:data_dssls,id',
            'field' => 'required|in:petugas_ppl,petugas_pml,petugas_entry',
            'value' => 'nullable|string'
        ]);

        $item         = DataDssls::find($request->id);
        $field        = $request->field;
        $item->$field = $request->value;
        $item->save();

        Cache::forget('count_data_dssls');

        return response()->json(['success' => true, 'message' => 'Petugas berhasil diupdate']);
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataDsrtImport;
use App\Exports\DataDsrtExport;
use App\Models\DataDsrt;
use Carbon\Carbon;

class DataDsrtController extends Controller
{
    public function import(Request $request)
    {
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
            'field' => 'required|in:ceklis_lap,ceklis_sosial,ceklis_ipds',
            'state' => 'required|in:0,1'
        ]);

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

    public function export()
    {
        return Excel::download(new DataDsrtExport, 'Export Data DSRT untuk IPDS.xlsx');
    }

    public function update(Request $request)
    {
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
            'field' => 'required|in:petugas_ppl,petugas_pml,petugas_susenas,petugas_seruti',
            'value' => 'nullable|string'
        ]);

        $item         = DataDsrt::find($request->id);
        $field        = $request->field;
        $item->$field = $request->value;
        $item->save();

        Cache::forget('count_data_dsrt');

        return response()->json(['success' => true, 'message' => 'Petugas berhasil diupdate']);
    }

    public function deleteBulk(Request $request)
    {
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
        DataDsrt::truncate();

        Cache::forget('count_data_dsrt');

        return response()->json(['success' => true, 'message' => 'Seluruh data berhasil dihapus']);
    }
}

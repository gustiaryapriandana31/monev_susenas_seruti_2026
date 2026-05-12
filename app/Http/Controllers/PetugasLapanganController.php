<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Imports\PetugasLapanganImport;
use App\Models\PetugasLapangan;
use Maatwebsite\Excel\Facades\Excel;

class PetugasLapanganController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new PetugasLapanganImport, $request->file('file_excel'));

        Cache::forget('dashboard_petugas_options');
        Cache::forget('count_petugas_lapangan');

        return back()->with('success', 'Data Petugas Lapangan Berhasil Diimport!');
    }

    public function deleteBulk(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:petugas_lapangans,id'
        ]);

        PetugasLapangan::whereIn('id', $request->ids)->delete();

        Cache::forget('dashboard_petugas_options');
        Cache::forget('count_petugas_lapangan');

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' data berhasil dihapus'
        ]);
    }

    public function deleteAll()
    {
        PetugasLapangan::truncate();

        Cache::forget('dashboard_petugas_options');
        Cache::forget('count_petugas_lapangan');

        return response()->json(['success' => true, 'message' => 'Seluruh data petugas lapangan berhasil dihapus']);
    }
}

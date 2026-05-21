<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Imports\PetugasLapanganImport;
use App\Models\PetugasLapangan;
use Maatwebsite\Excel\Facades\Excel;

class PetugasLapanganController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $route = $request->route()->getActionMethod();

            if ($route === 'import') {
                if (!$user->isSuperAdmin() && !$user->isAdminSosial()) {
                    abort(403, 'Akses ditolak. Hanya Super Admin dan Admin Sosial yang dapat mengimpor petugas lapangan.');
                }
            } else {
                if (!$user->isSuperAdmin() && !$user->isAdminSosial()) {
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya Super Admin dan Admin Sosial yang dapat mengubah data petugas.'], 403);
                    }
                    abort(403, 'Akses ditolak. Hanya Super Admin dan Admin Sosial yang dapat mengubah data petugas.');
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

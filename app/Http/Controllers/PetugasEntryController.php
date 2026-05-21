<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Imports\PetugasEntryImport;
use App\Models\PetugasEntry;
use Maatwebsite\Excel\Facades\Excel;

class PetugasEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $route = $request->route()->getActionMethod();

            if ($route === 'import') {
                if (!$user->isSuperAdmin() && !$user->isAdminIpds()) {
                    abort(403, 'Akses ditolak. Hanya Super Admin dan Admin IPDS yang dapat mengimpor petugas entry.');
                }
            } else {
                if (!$user->isSuperAdmin() && !$user->isAdminIpds()) {
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya Super Admin dan Admin IPDS yang dapat mengubah data petugas.'], 403);
                    }
                    abort(403, 'Akses ditolak. Hanya Super Admin dan Admin IPDS yang dapat mengubah data petugas.');
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

        Excel::import(new PetugasEntryImport, $request->file('file_excel'));

        Cache::forget('dashboard_petugas_options');
        Cache::forget('count_petugas_entry');

        return back()->with('success', 'Data Petugas Entry Berhasil Diimport!');
    }

    public function deleteBulk(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:petugas_entries,id'
        ]);

        PetugasEntry::whereIn('id', $request->ids)->delete();

        Cache::forget('dashboard_petugas_options');
        Cache::forget('count_petugas_entry');

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' data berhasil dihapus'
        ]);
    }

    public function deleteAll()
    {
        PetugasEntry::truncate();

        Cache::forget('dashboard_petugas_options');
        Cache::forget('count_petugas_entry');

        return response()->json(['success' => true, 'message' => 'Seluruh data petugas entry berhasil dihapus']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PetugasLapanganImport;
use Maatwebsite\Excel\Facades\Excel;

class PetugasLapanganController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new PetugasLapanganImport, $request->file('file_excel'));

        return back()->with('success', 'Data Petugas Lapangan Berhasil Diimport!');
    }
}

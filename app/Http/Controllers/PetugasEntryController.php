<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PetugasEntryImport;
use Maatwebsite\Excel\Facades\Excel;

class PetugasEntryController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new PetugasEntryImport, $request->file('file_excel'));

        return back()->with('success', 'Data Petugas Entry Berhasil Diimport!');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PetugasLapangan;
use App\Models\PetugasEntry;
use App\Models\DataDssls;
use App\Models\DataDsrt;

class DashboardController extends Controller
{
    public function index()
    {
        $petugasLapangans = PetugasLapangan::all();
        $petugasEntries = PetugasEntry::all();
        $dataDssls = DataDssls::with(['ppl', 'pml', 'entry'])->get();
        $dataDsrt = DataDsrt::with(['ppl', 'pml', 'susenas', 'seruti'])->get();

        return view('dashboard.index', compact(
            'petugasLapangans',
            'petugasEntries',
            'dataDssls',
            'dataDsrt'
        ));
    }
}

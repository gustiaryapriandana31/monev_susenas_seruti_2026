<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetugasEntryController;
use App\Http\Controllers\PetugasLapanganController;
use App\Http\Controllers\DataDsslsController;
use App\Http\Controllers\DataDsrtController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\LoginController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/datatable/lapangan', [DashboardController::class, 'datatableLapangan'])->name('dashboard.datatable_lapangan');
    Route::get('/dashboard/datatable/entry', [DashboardController::class, 'datatableEntry'])->name('dashboard.datatable_entry');
    Route::get('/dashboard/datatable/dssls', [DashboardController::class, 'datatableDssls'])->name('dashboard.datatable_dssls');
    Route::get('/dashboard/datatable/dsrt', [DashboardController::class, 'datatableDsrt'])->name('dashboard.datatable_dsrt');
    Route::get('/dashboard/petugas-options', [DashboardController::class, 'petugasOptions'])->name('dashboard.petugas_options');
    Route::post('/import-petugas-lapangan', [PetugasLapanganController::class, 'import'])->name('petugas_lapangan.import');

    Route::post('/import-petugas-entry', [PetugasEntryController::class, 'import'])->name('petugas_entry.import');

    Route::post('/import-data-dssls', [DataDsslsController::class, 'import'])->name('data_dssls.import');

    Route::post('/import-data-dsrt', [DataDsrtController::class, 'import'])->name('data_dsrt.import');

    // Toggle Ceklis Routes
    Route::post('/data-dssls/toggle-ceklis', [DataDsslsController::class, 'toggleCeklis'])->name('data_dssls.toggle_ceklis');
    Route::post('/data-dsrt/toggle-ceklis', [DataDsrtController::class, 'toggleCeklis'])->name('data_dsrt.toggle_ceklis');

    // Export Routes
    Route::get('/data-dssls/export-ori', [DataDsslsController::class, 'exportOri'])->name('data_dssls.export_ori');
    Route::get('/data-dssls/export', [DataDsslsController::class, 'export'])->name('data_dssls.export');
    Route::get('/data-dsrt/export-ipds', [DataDsrtController::class, 'exportIPDS'])->name('data_dsrt.export_ipds');
    Route::get('/data-dsrt/export-sosial', [DataDsrtController::class, 'exportSosial'])->name('data_dsrt.export_sosial');
    Route::get('/data-dsrt/export-sosial-kab', [DataDsrtController::class, 'exportSosialKab'])->name('data_dsrt.export_sosial_kab');
    Route::get('/data-dsrt/export-lapangan', [DataDsrtController::class, 'exportLapangan'])->name('data_dsrt.export_lapangan');
    Route::get('/data-dsrt/export-pemeriksaan', [DataDsrtController::class, 'exportPemeriksaan'])->name('data_dsrt.export_pemeriksaan');

    // Update Routes
    Route::post('/data-dssls/update', [DataDsslsController::class, 'update'])->name('data_dssls.update');
    Route::post('/data-dssls/update-inline', [DataDsslsController::class, 'updateInline'])->name('data_dssls.update_inline');
    Route::post('/data-dsrt/update', [DataDsrtController::class, 'update'])->name('data_dsrt.update');
    Route::post('/data-dsrt/update-inline', [DataDsrtController::class, 'updateInline'])->name('data_dsrt.update_inline');

    // Delete Routes
    Route::post('/petugas-lapangan/delete-bulk', [PetugasLapanganController::class, 'deleteBulk'])->name('petugas_lapangan.delete_bulk');
    Route::post('/petugas-lapangan/delete-all', [PetugasLapanganController::class, 'deleteAll'])->name('petugas_lapangan.delete_all');
    Route::post('/petugas-entry/delete-bulk', [PetugasEntryController::class, 'deleteBulk'])->name('petugas_entry.delete_bulk');
    Route::post('/petugas-entry/delete-all', [PetugasEntryController::class, 'deleteAll'])->name('petugas_entry.delete_all');
    Route::post('/data-dssls/delete-bulk', [DataDsslsController::class, 'deleteBulk'])->name('data_dssls.delete_bulk');
    Route::post('/data-dssls/delete-all', [DataDsslsController::class, 'deleteAll'])->name('data_dssls.delete_all');
    Route::post('/data-dsrt/delete-bulk', [DataDsrtController::class, 'deleteBulk'])->name('data_dsrt.delete_bulk');
    Route::post('/data-dsrt/delete-all', [DataDsrtController::class, 'deleteAll'])->name('data_dsrt.delete_all');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

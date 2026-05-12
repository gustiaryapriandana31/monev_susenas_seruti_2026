<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetugasEntryController;
use App\Http\Controllers\PetugasLapanganController;
use App\Http\Controllers\DataDsslsController;
use App\Http\Controllers\DataDsrtController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');



Route::post('/import-petugas-lapangan', [PetugasLapanganController::class, 'import'])->name('petugas_lapangan.import');

Route::post('/import-petugas-entry', [PetugasEntryController::class, 'import'])->name('petugas_entry.import');

Route::post('/import-data-dssls', [DataDsslsController::class, 'import'])->name('data_dssls.import');

Route::post('/import-data-dsrt', [DataDsrtController::class, 'import'])->name('data_dsrt.import');

// Toggle Ceklis Routes
Route::post('/data-dssls/toggle-ceklis', [DataDsslsController::class, 'toggleCeklis'])->name('data_dssls.toggle_ceklis');
Route::post('/data-dsrt/toggle-ceklis', [DataDsrtController::class, 'toggleCeklis'])->name('data_dsrt.toggle_ceklis');

// Export Routes
Route::get('/data-dssls/export', [DataDsslsController::class, 'export'])->name('data_dssls.export');
Route::get('/data-dsrt/export', [DataDsrtController::class, 'export'])->name('data_dsrt.export');

// Update Routes
Route::post('/data-dssls/update', [DataDsslsController::class, 'update'])->name('data_dssls.update');
Route::post('/data-dssls/update-inline', [DataDsslsController::class, 'updateInline'])->name('data_dssls.update_inline');
Route::post('/data-dsrt/update', [DataDsrtController::class, 'update'])->name('data_dsrt.update');
Route::post('/data-dsrt/update-inline', [DataDsrtController::class, 'updateInline'])->name('data_dsrt.update_inline');

// Delete Routes
Route::post('/data-dssls/delete-bulk', [DataDsslsController::class, 'deleteBulk'])->name('data_dssls.delete_bulk');
Route::post('/data-dssls/delete-all', [DataDsslsController::class, 'deleteAll'])->name('data_dssls.delete_all');
Route::post('/data-dsrt/delete-bulk', [DataDsrtController::class, 'deleteBulk'])->name('data_dsrt.delete_bulk');
Route::post('/data-dsrt/delete-all', [DataDsrtController::class, 'deleteAll'])->name('data_dsrt.delete_all');

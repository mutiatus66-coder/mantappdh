<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RGNController as RGN;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PenilaiController;
use App\Http\Controllers\InovasiController;
use App\Http\Controllers\IndikatorController;

// ── Auth Pages ─────────────────────────────────────────────
Route::get('/login',          fn() => view('login'))->name('login');
Route::get('/sign-in',        fn() => view('sign-in'));
Route::get('/sign-up',        fn() => view('sign-up'));
Route::get('/reset-password', fn() => view('reset-password'));
Route::get('/new-password',   fn() => view('new-password'));

// ── Dashboard ──────────────────────────────────────────────
Route::get('/',      fn() => view('dashboard'))->name('dashboard');
Route::get('/index', fn() => view('index'))->name('index');

// ── RGN Main Routes (Sub Event & Bidang) ───────────────────
Route::prefix('')->name('rgn.')->group(function () {

    // Sub Event
    Route::resource('sub-event', RGN::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy'])
        ->names([
            'index'   => 'sub-event.index',
            'store'   => 'sub-event.store',
            'edit'    => 'sub-event.edit',
            'update'  => 'sub-event.update',
            'destroy' => 'sub-event.destroy',
        ]);

    // Bidang
    Route::controller(RGN::class)->prefix('bidang')->name('bidang.')->group(function () {
        Route::get('/',          'bidang')->name('index');
        Route::post('/store',    'storeBidang')->name('store');
        Route::get('/{id}/edit', 'editBidang')->name('edit');
        Route::put('/{id}',      'updateBidang')->name('update');
        Route::delete('/{id}',   'destroyBidang')->name('destroy');
    });

});

// ── Event ──────────────────────────────────────────────────
Route::resource('event', EventController::class)
    ->only(['index', 'store', 'update', 'destroy']);

// ── User Management ────────────────────────────────────────
Route::resource('user', UserController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::get('user/{id}/login-as', [UserController::class, 'loginAs'])
    ->name('user.login-as');

// ── Penilai ────────────────────────────────────────────────
Route::resource('penilai', PenilaiController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->names([
        'index'   => 'rgn.penilai.index',
        'store'   => 'rgn.penilai.store',
        'update'  => 'rgn.penilai.update',
        'destroy' => 'rgn.penilai.destroy',
    ]);

// ── Pengumuman ─────────────────────────────────────────────
Route::resource('pengumuman', PengumumanController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->names([
        'index'   => 'admin.pengumuman.index',
        'store'   => 'admin.pengumuman.store',
        'update'  => 'admin.pengumuman.update',
        'destroy' => 'admin.pengumuman.destroy',
    ]);

// ── Penilaian ──────────────────────────────────────────────
Route::prefix('penilaian')->name('penilaian.')->group(function () {

    // Tahap 1
    Route::get('/tahap-1',                  [RGN::class, 'penilaianTahap1'])->name('tahap.1.index');
    Route::get('/tahap-1/{id}',             [RGN::class, 'penilaianTahap1Show'])->name('tahap.1.show');
    Route::post('/tahap-1/{id}/simpan',     [RGN::class, 'penilaianTahap1Simpan'])->name('tahap.1.simpan');

    // Tahap 2
    Route::get('/tahap-2',                  [RGN::class, 'penilaianTahap2'])->name('tahap.2.index');
    Route::get('/tahap-2/{id}',             [RGN::class, 'penilaianTahap2Show'])->name('tahap.2.show');

});

// ── Inovasi & Rekap ────────────────────────────────────────
Route::prefix('inovasi')->name('rgn.inovasi.')->group(function () {
    Route::get('/riwayat',     [InovasiController::class, 'riwayat'])->name('riwayat');
    Route::get('/rekap-nilai', [InovasiController::class, 'rekapNilai'])->name('rekapnilai');
    Route::get('/usulan/{subEventId}', [InovasiController::class, 'usulan'])->name('usulan');
});

// Indikator Tahap 1
Route::get('/indikator/tahap-1', [IndikatorController::class, 'tahap1'])->name('indikator.tahap1');
Route::get('/indikator/tahap-1/{id}/indikator', [IndikatorController::class, 'detailIndikator1'])->name('indikator.tahap1.indikator');
Route::get('/indikator/tahap-1/{id}/formulasi', [IndikatorController::class, 'detailFormulasi1'])->name('indikator.tahap1.formulasi');

// Indikator Tahap 2
Route::get('/indikator/tahap-2', [IndikatorController::class, 'tahap2'])->name('indikator.tahap2');
Route::get('/indikator/tahap-2/{id}/indikator', [IndikatorController::class, 'detailIndikator2'])->name('indikator.tahap2.indikator');
Route::get('/indikator/tahap-2/{id}/formulasi', [IndikatorController::class, 'detailFormulasi2'])->name('indikator.tahap2.formulasi');

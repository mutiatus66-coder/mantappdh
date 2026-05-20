<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubEventController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PenilaiController;
use App\Http\Controllers\InovasiController;
use App\Http\Controllers\IndikatorController;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/login',          fn() => view('login'))->name('login');
Route::get('/sign-in',        fn() => view('sign-in'));
Route::get('/sign-up',        fn() => view('sign-up'));
Route::get('/reset-password', fn() => view('reset-password'));
Route::get('/new-password',   fn() => view('new-password'));

// ── Dashboard ─────────────────────────────────────────────────────────────────
Route::get('/',      fn() => view('dashboard'))->name('dashboard');
Route::get('/index', fn() => view('index'))->name('index');

// ── Sub Event ─────────────────────────────────────────────────────────────────
Route::prefix('sub-event')->name('sub-event.')->group(function () {
    Route::get('/',          [SubEventController::class, 'index'])   ->name('index');
    Route::post('/',         [SubEventController::class, 'store'])   ->name('store');
    Route::get('/{id}/edit', [SubEventController::class, 'edit'])    ->name('edit');
    Route::put('/{id}',      [SubEventController::class, 'update'])  ->name('update');
    Route::delete('/{id}',   [SubEventController::class, 'destroy']) ->name('destroy');
});

// ── Bidang ────────────────────────────────────────────────────────────────────
Route::prefix('bidang')->name('bidang.')->group(function () {
    Route::get('/',          [BidangController::class, 'index'])   ->name('index');
    Route::post('/',         [BidangController::class, 'store'])   ->name('store');
    Route::get('/{id}/edit', [BidangController::class, 'edit'])    ->name('edit');
    Route::put('/{id}',      [BidangController::class, 'update'])  ->name('update');
    Route::delete('/{id}',   [BidangController::class, 'destroy']) ->name('destroy');
});

// ── Penilaian ─────────────────────────────────────────────────────────────────
Route::prefix('penilaian')->name('penilaian.')->group(function () {
    Route::get('/tahap-1',              [PenilaianController::class, 'tahap1'])       ->name('tahap1.index');
    Route::get('/tahap-1/{id}',         [PenilaianController::class, 'tahap1Show'])   ->name('tahap1.show');
    Route::post('/tahap-1/{id}/simpan', [PenilaianController::class, 'tahap1Simpan']) ->name('tahap1.simpan');

    Route::get('/tahap-2',      [PenilaianController::class, 'tahap2'])     ->name('tahap2.index');
    Route::get('/tahap-2/{id}', [PenilaianController::class, 'tahap2Show']) ->name('tahap2.show');
});

// ── Penilai ───────────────────────────────────────────────────────────────────
Route::prefix('penilai')->name('penilai.')->group(function () {
    Route::get('/',        [PenilaiController::class, 'index'])   ->name('index');
    Route::post('/',       [PenilaiController::class, 'store'])   ->name('store');
    Route::put('/{id}',    [PenilaiController::class, 'update'])  ->name('update');
    Route::delete('/{id}', [PenilaiController::class, 'destroy']) ->name('destroy');
});

// ── Event ─────────────────────────────────────────────────────────────────────
Route::prefix('event')->name('event.')->group(function () {
    Route::get('/',        [EventController::class, 'index'])   ->name('index');
    Route::post('/',       [EventController::class, 'store'])   ->name('store');
    Route::put('/{id}',    [EventController::class, 'update'])  ->name('update');
    Route::delete('/{id}', [EventController::class, 'destroy']) ->name('destroy');
});

// ── User ──────────────────────────────────────────────────────────────────────
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/',              [UserController::class, 'index'])   ->name('index');
    Route::post('/',             [UserController::class, 'store'])   ->name('store');
    Route::put('/{id}',          [UserController::class, 'update'])  ->name('update');
    Route::delete('/{id}',       [UserController::class, 'destroy']) ->name('destroy');
    Route::get('/{id}/login-as', [UserController::class, 'loginAs']) ->name('login-as');
});

// ── Pengumuman ────────────────────────────────────────────────────────────────
Route::prefix('pengumuman')->name('admin.pengumuman.')->group(function () {
    Route::get('/',        [PengumumanController::class, 'index'])   ->name('index');
    Route::post('/',       [PengumumanController::class, 'store'])   ->name('store');
    Route::put('/{id}',    [PengumumanController::class, 'update'])  ->name('update');
    Route::delete('/{id}', [PengumumanController::class, 'destroy']) ->name('destroy');
});

// ── Inovasi ───────────────────────────────────────────────────────────────────
Route::prefix('inovasi')->name('inovasi.')->group(function () {
    Route::get('/riwayat',          [InovasiController::class, 'riwayat'])    ->name('riwayat');
    Route::get('/rekap-nilai',      [InovasiController::class, 'rekapNilai']) ->name('rekapnilai');
});
    Route::get('/inovasi/usulan-riwayat/{subEventId}', [InovasiController::class, 'usulanRiwayat']);
    Route::get('/inovasi/usulan-nilai/{subEventId}', [InovasiController::class, 'usulanNilai']);

// ── Indikator ─────────────────────────────────────────────────────────────────
Route::prefix('indikator')->name('indikator.')->group(function () {

    // Tahap 1 — halaman utama
    Route::get('/tahap-1', [IndikatorController::class, 'tahap1'])->name('tahap1');

    // Tahap 1 — Detail Inovasi (daftar indikator per sub event)
    Route::get('/tahap-1/{subEventId}/inovasi',              [IndikatorController::class, 'detailInovasi'])  ->name('tahap1.inovasi');
    Route::get('/tahap-1/{subEventId}/inovasi/create',       [IndikatorController::class, 'inovasiCreate'])  ->name('tahap1.inovasi.create');
    Route::post('/tahap-1/{subEventId}/inovasi',             [IndikatorController::class, 'inovasiStore'])   ->name('tahap1.inovasi.store');
    Route::get('/tahap-1/{subEventId}/inovasi/{id}/edit',    [IndikatorController::class, 'inovasiEdit'])    ->name('tahap1.inovasi.edit');
    Route::put('/tahap-1/{subEventId}/inovasi/{id}',         [IndikatorController::class, 'inovasiUpdate'])  ->name('tahap1.inovasi.update');
    Route::delete('/tahap-1/{subEventId}/inovasi/{id}',      [IndikatorController::class, 'inovasiDestroy']) ->name('tahap1.inovasi.destroy');

    // Tahap 1 — Detail Indikator (keterangan + nilai min/maks)
    Route::get('/tahap-1/{subEventId}/detail/{indikatorId}',               [IndikatorController::class, 'detailIndikator'])       ->name('tahap1.detail');
    Route::get('/tahap-1/{subEventId}/detail/{indikatorId}/create',        [IndikatorController::class, 'detailIndikatorCreate']) ->name('tahap1.detail.create');
    Route::post('/tahap-1/{subEventId}/detail/{indikatorId}',              [IndikatorController::class, 'detailIndikatorStore'])  ->name('tahap1.detail.store');
    Route::get('/tahap-1/{subEventId}/detail/{indikatorId}/{id}/edit',     [IndikatorController::class, 'detailIndikatorEdit'])   ->name('tahap1.detail.edit');
    Route::put('/tahap-1/{subEventId}/detail/{indikatorId}/{id}',          [IndikatorController::class, 'detailIndikatorUpdate']) ->name('tahap1.detail.update');
    Route::delete('/tahap-1/{subEventId}/detail/{indikatorId}/{id}',       [IndikatorController::class, 'detailIndikatorDestroy'])->name('tahap1.detail.destroy');

// ─────────────────────────────
// TAHAP 2 ─ Halaman utama, Detail indikator, Detail formulasi, Simpan/update formulasi, Ambil data formulasi untuk modal
// ─────────────────────────────
    // Tahap 2 — halaman utama
    Route::get('/tahap-2',                                [IndikatorController::class, 'tahap2'])                ->name('tahap2');

    // Tahap 2 — Detail Indikator
    Route::get('/tahap-2/{id}/indikator',                 [IndikatorController::class, 'detailIndikator2'])      ->name('tahap2.indikator');
    Route::post('/tahap-2/{subEventId}/indikator',        [IndikatorController::class, 'indikatorTahap2Store'])  ->name('tahap2.indikator.store');
    Route::put('/tahap-2/{subEventId}/indikator/{id}',    [IndikatorController::class, 'indikatorTahap2Update']) ->name('tahap2.indikator.update');
    Route::delete('/tahap-2/{subEventId}/indikator/{id}', [IndikatorController::class, 'indikatorTahap2Destroy'])->name('tahap2.indikator.destroy');

    // Tahap 2 — Formulasi
    Route::post('/tahap-2/{subEventId}/formulasi',        [IndikatorController::class, 'formulasiTahap2Store'])  ->name('tahap2.formulasi.store');
    Route::get('/tahap-2/{subEventId}/formulasi/get',     [IndikatorController::class, 'formulasiTahap2Get'])    ->name('tahap2.formulasi.get');
    Route::post('/tahap-1/{subEventId}/formulasi',        [IndikatorController::class, 'formulasiTahap1Store'])->name('tahap1.formulasi.store');
    Route::get('/tahap-1/{subEventId}/formulasi/get',       [IndikatorController::class, 'formulasiTahap1Get'])  ->name('tahap1.formulasi.get');

});
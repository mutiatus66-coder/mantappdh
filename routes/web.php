<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SubEventController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PenilaiController;
use App\Http\Controllers\InovasiController;
use App\Http\Controllers\IndikatorController;

// ══════════════════════════════════════════════════════════════════════════════
// PUBLIK — Tidak perlu login
// ══════════════════════════════════════════════════════════════════════════════
Route::get('/',           fn() => view('dashboard'))->name('landing');
Route::get('/login',          fn() => view('sign-in'))->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::get('/sign-in',        fn() => view('sign-in'))->name('sign-in');
Route::get('/sign-up',        fn() => view('sign-up'))->name('sign-up');
Route::get('/reset-password', fn() => view('reset-password'))->name('reset-password');
Route::get('/new-password',   fn() => view('new-password'))->name('new-password');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ══════════════════════════════════════════════════════════════════════════════
// DEV ONLY — Hapus sebelum rilis!
// ══════════════════════════════════════════════════════════════════════════════

Route::get('/dev-login-admin', function () {
    $admin = \App\Models\User::query()->where('hak_akses', 'admin_bapperida')->first();

    if (!$admin) {
        return 'Belum ada user dengan hak_akses admin_bapperida di database.';
    }

    Auth::login($admin);
    return redirect('/');
})->name('dev-login-admin'); // ← nama diubah, bukan 'index'

// ══════════════════════════════════════════════════════════════════════════════
// PROTECTED — Wajib login
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware(['auth'])->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::get('/index', fn() => view('index'))->name('index');

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::get('/admin', function () {
        if (!Auth::user()->isAdminBapperida()) {
            abort(403, 'Akses ditolak.');
        }
        return view('admin.index');
    })->name('admin.index');

    // ── Event ─────────────────────────────────────────────────────────────────
    Route::prefix('event')->name('event.')->group(function () {
        Route::get('/',        [EventController::class, 'index'])   ->name('index');
        Route::post('/',       [EventController::class, 'store'])   ->name('store');
        Route::put('/{id}',    [EventController::class, 'update'])  ->name('update');
        Route::delete('/{id}', [EventController::class, 'destroy']) ->name('destroy');
    });

    // ── Sub Event ─────────────────────────────────────────────────────────────
    Route::prefix('sub-event')->name('sub-event.')->group(function () {
        Route::get('/',          [SubEventController::class, 'index'])   ->name('index');
        Route::post('/',         [SubEventController::class, 'store'])   ->name('store');
        Route::get('/{id}/edit', [SubEventController::class, 'edit'])    ->name('edit');
        Route::put('/{id}',      [SubEventController::class, 'update'])  ->name('update');
        Route::delete('/{id}',   [SubEventController::class, 'destroy']) ->name('destroy');
    });

    // ── Bidang ────────────────────────────────────────────────────────────────
    Route::prefix('bidang')->name('bidang.')->group(function () {
        Route::get('/',          [BidangController::class, 'index'])   ->name('index');
        Route::post('/',         [BidangController::class, 'store'])   ->name('store');
        Route::get('/{id}/edit', [BidangController::class, 'edit'])    ->name('edit');
        Route::put('/{id}',      [BidangController::class, 'update'])  ->name('update');
        Route::delete('/{id}',   [BidangController::class, 'destroy']) ->name('destroy');
    });

    // ── User ──────────────────────────────────────────────────────────────────
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/',              [UserController::class, 'index'])   ->name('index');
        Route::post('/',             [UserController::class, 'store'])   ->name('store');
        Route::put('/{id}',          [UserController::class, 'update'])  ->name('update');
        Route::delete('/{id}',       [UserController::class, 'destroy']) ->name('destroy');
        Route::get('/{id}/login-as', [UserController::class, 'loginAs']) ->name('login-as');
    });

    // ── Penilai ───────────────────────────────────────────────────────────────
    Route::prefix('penilai')->name('penilai.')->group(function () {
        Route::get('/',        [PenilaiController::class, 'index'])   ->name('index');
        Route::post('/',       [PenilaiController::class, 'store'])   ->name('store');
        Route::put('/{id}',    [PenilaiController::class, 'update'])  ->name('update');
        Route::delete('/{id}', [PenilaiController::class, 'destroy']) ->name('destroy');
    });

    // ── Pengumuman ────────────────────────────────────────────────────────────
    Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
        Route::get('/',        [PengumumanController::class, 'index'])   ->name('index');
        Route::post('/',       [PengumumanController::class, 'store'])   ->name('store');
        Route::put('/{id}',    [PengumumanController::class, 'update'])  ->name('update');
        Route::delete('/{id}', [PengumumanController::class, 'destroy']) ->name('destroy');
    });

    // ── Inovasi ───────────────────────────────────────────────────────────────
    Route::prefix('inovasi')->name('inovasi.')->group(function () {
        Route::get('/riwayat',                           [InovasiController::class, 'riwayat'])       ->name('riwayat');
        Route::get('/rekap-nilai',                       [InovasiController::class, 'rekapNilai'])    ->name('rekapnilai');
        Route::get('/usulan-riwayat/{subEventId}',       [InovasiController::class, 'usulanRiwayat'])->name('usulan-riwayat');
        Route::get('/usulan-nilai/{subEventId}',         [InovasiController::class, 'usulanNilai'])  ->name('usulan-nilai');
    });

    // ── Penilaian ─────────────────────────────────────────────────────────────
    Route::prefix('penilaian')->name('penilaian.')->group(function () {
        Route::get('/tahap-1',              [PenilaianController::class, 'tahap1'])       ->name('tahap1.index');
        Route::get('/tahap-1/{id}',         [PenilaianController::class, 'tahap1Show'])   ->name('tahap1.show');
        Route::post('/tahap-1/{id}/simpan', [PenilaianController::class, 'tahap1Simpan']) ->name('tahap1.simpan');

        Route::get('/tahap-2',              [PenilaianController::class, 'tahap2'])       ->name('tahap2.index');
        Route::get('/tahap-2/{id}',         [PenilaianController::class, 'tahap2Show'])   ->name('tahap2.show');
    });

    // ── Indikator ─────────────────────────────────────────────────────────────
    Route::prefix('indikator')->name('indikator.')->group(function () {

        // Tahap 1 — Halaman utama
        Route::get('/tahap-1', [IndikatorController::class, 'tahap1'])->name('tahap1');

        // Tahap 1 — Detail Inovasi
        Route::get('/tahap-1/{subEventId}/inovasi',           [IndikatorController::class, 'detailInovasi'])    ->name('tahap1.inovasi');
        Route::get('/tahap-1/{subEventId}/inovasi/create',    [IndikatorController::class, 'inovasiCreate'])    ->name('tahap1.inovasi.create');
        Route::post('/tahap-1/{subEventId}/inovasi',          [IndikatorController::class, 'inovasiStore'])     ->name('tahap1.inovasi.store');
        Route::get('/tahap-1/{subEventId}/inovasi/{id}/edit', [IndikatorController::class, 'inovasiEdit'])      ->name('tahap1.inovasi.edit');
        Route::put('/tahap-1/{subEventId}/inovasi/{id}',      [IndikatorController::class, 'inovasiUpdate'])    ->name('tahap1.inovasi.update');
        Route::delete('/tahap-1/{subEventId}/inovasi/{id}',   [IndikatorController::class, 'inovasiDestroy'])   ->name('tahap1.inovasi.destroy');

        // Tahap 1 — Detail Indikator
        Route::get('/tahap-1/{subEventId}/detail/{indikatorId}',              [IndikatorController::class, 'detailIndikator'])       ->name('tahap1.detail');
        Route::get('/tahap-1/{subEventId}/detail/{indikatorId}/create',       [IndikatorController::class, 'detailIndikatorCreate']) ->name('tahap1.detail.create');
        Route::post('/tahap-1/{subEventId}/detail/{indikatorId}',             [IndikatorController::class, 'detailIndikatorStore'])  ->name('tahap1.detail.store');
        Route::get('/tahap-1/{subEventId}/detail/{indikatorId}/{id}/edit',    [IndikatorController::class, 'detailIndikatorEdit'])   ->name('tahap1.detail.edit');
        Route::put('/tahap-1/{subEventId}/detail/{indikatorId}/{id}',         [IndikatorController::class, 'detailIndikatorUpdate']) ->name('tahap1.detail.update');
        Route::delete('/tahap-1/{subEventId}/detail/{indikatorId}/{id}',      [IndikatorController::class, 'detailIndikatorDestroy'])->name('tahap1.detail.destroy');

        // Tahap 1 — Formulasi
        Route::post('/tahap-1/{subEventId}/formulasi',     [IndikatorController::class, 'formulasiTahap1Store'])->name('tahap1.formulasi.store');
        Route::get('/tahap-1/{subEventId}/formulasi/get',  [IndikatorController::class, 'formulasiTahap1Get'])  ->name('tahap1.formulasi.get');

        // Tahap 2 — Halaman utama
        Route::get('/tahap-2', [IndikatorController::class, 'tahap2'])->name('tahap2');

        // Tahap 2 — Detail Indikator
        Route::get('/tahap-2/{id}/indikator',                 [IndikatorController::class, 'detailIndikator2'])      ->name('tahap2.indikator');
        Route::post('/tahap-2/{subEventId}/indikator',        [IndikatorController::class, 'indikatorTahap2Store'])  ->name('tahap2.indikator.store');
        Route::put('/tahap-2/{subEventId}/indikator/{id}',    [IndikatorController::class, 'indikatorTahap2Update']) ->name('tahap2.indikator.update');
        Route::delete('/tahap-2/{subEventId}/indikator/{id}', [IndikatorController::class, 'indikatorTahap2Destroy'])->name('tahap2.indikator.destroy');

        // Tahap 2 — Formulasi
        Route::post('/tahap-2/{subEventId}/formulasi',     [IndikatorController::class, 'formulasiTahap2Store'])->name('tahap2.formulasi.store');
        Route::get('/tahap-2/{subEventId}/formulasi/get',  [IndikatorController::class, 'formulasiTahap2Get'])  ->name('tahap2.formulasi.get');

        // Master (nested di dalam indikator)
        Route::prefix('master')->name('master.')->group(function () {
            Route::resource('event',     EventController::class)   ->except(['create', 'show']);
            Route::resource('sub-event', SubEventController::class)->except(['create', 'show']);
            Route::resource('bidang',    BidangController::class)  ->except(['create', 'show']);
            Route::get('bidang/by-sub-event/{subEventId}', [BidangController::class, 'bySubEvent'])->name('bidang.by-sub-event');
        });

    }); // end indikator

}); // end middleware auth
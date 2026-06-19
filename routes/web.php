<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubEventController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PenilaiController;
use App\Http\Controllers\InovasiController;
use App\Http\Controllers\IndikatorController;
use App\Http\Controllers\PengumumanLuarController;
use App\Http\Controllers\BuletinController;

// ══════════════════════════════════════════════════════════════════════════════
// PUBLIK — Tidak perlu login
// ══════════════════════════════════════════════════════════════════════════════

Route::get('/',               fn() => view('dashboard'))      ->name('landing');
Route::get('/sign-in',        fn() => view('sign-in'))        ->name('sign-in');
Route::get('/sign-up',        fn() => view('sign-up'))        ->name('sign-up');
Route::get('/reset-password', fn() => view('reset-password')) ->name('reset-password');
Route::get('/new-password',   fn() => view('new-password'))   ->name('new-password');

Route::get('/login',  fn() => view('sign-in'))                ->name('login');
Route::post('/login', [AuthController::class, 'login'])       ->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])      ->name('logout')->middleware('auth');
Route::post('/sign-up', [AuthController::class, 'register'])  ->name('register');

// Pengumuman untuk publik
Route::get('/buletin',      [BuletinController::class, 'index'])->name('public.pengumuman.index');
Route::get('/buletin/{id}', [BuletinController::class, 'show']) ->name('public.pengumuman.show');

Route::get('/dev-login/{email}', function ($email) {
    $user = \App\Models\User::where('email', $email)->firstOrFail();
    Auth::login($user);
    return redirect('/inovasi/riwayat');
});

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
    Route::middleware(['role:admin_bapperida'])->prefix('event')->name('event.')->group(function () {
        Route::get('/',        [EventController::class, 'index'])   ->name('index');
        Route::post('/',       [EventController::class, 'store'])   ->name('store');
        Route::put('/{id}',    [EventController::class, 'update'])  ->name('update');
        Route::delete('/{id}', [EventController::class, 'destroy']) ->name('destroy');
    });

    // ── Sub Event ─────────────────────────────────────────────────────────────
    Route::middleware(['role:admin_bapperida'])->prefix('sub-event')->name('sub-event.')->group(function () {
        Route::get('/',          [SubEventController::class, 'index'])   ->name('index');
        Route::post('/',         [SubEventController::class, 'store'])   ->name('store');
        Route::get('/{id}/edit', [SubEventController::class, 'edit'])    ->name('edit');
        Route::put('/{id}',      [SubEventController::class, 'update'])  ->name('update');
        Route::delete('/{id}',   [SubEventController::class, 'destroy']) ->name('destroy');
    });

    // ── Bidang ────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin_bapperida'])->prefix('bidang')->name('bidang.')->group(function () {
        Route::get('/',          [BidangController::class, 'index'])   ->name('index');
        Route::post('/',         [BidangController::class, 'store'])   ->name('store');
        Route::get('/{id}/edit', [BidangController::class, 'edit'])    ->name('edit');
        Route::put('/{id}',      [BidangController::class, 'update'])  ->name('update');
        Route::delete('/{id}',   [BidangController::class, 'destroy']) ->name('destroy');
    });
    Route::get('/user/login-back', [UserController::class, 'loginBack'])->name('user.login-back');
    // ── User ──────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin_bapperida'])->prefix('user')->name('user.')->group(function () {
        Route::get('/',              [UserController::class, 'index'])   ->name('index');
        Route::post('/',             [UserController::class, 'store'])   ->name('store');
        Route::put('/{id}',          [UserController::class, 'update'])  ->name('update');
        Route::delete('/{id}',       [UserController::class, 'destroy']) ->name('destroy');
        Route::get('/{id}/login-as', [UserController::class, 'loginAs'])  ->name('login-as');
    });

    // ── Penilai — hanya admin_bapperida ──────────────────────────────────────
    Route::middleware(['role:admin_bapperida'])->group(function () {
        Route::get('/penilai',              [PenilaiController::class, 'index'])   ->name('penilai.index');
        Route::get('/penilai/{subEventId}', [PenilaiController::class, 'detail'])  ->name('penilai.detail');
        Route::post('/penilai',             [PenilaiController::class, 'store'])   ->name('penilai.store');
        Route::put('/penilai/{id}',         [PenilaiController::class, 'update'])  ->name('penilai.update');
        Route::delete('/penilai/{id}',      [PenilaiController::class, 'destroy']) ->name('penilai.destroy');
    });

    // ── Pengumuman — hanya admin_bapperida ───────────────────────────────────
    Route::middleware(['role:admin_bapperida'])->group(function () {
        Route::get('/pengumuman',         [PengumumanController::class, 'index'])   ->name('pengumuman.index');
        Route::post('/pengumuman',        [PengumumanController::class, 'store'])   ->name('pengumuman.store');
        Route::put('/pengumuman/{id}',    [PengumumanController::class, 'update'])  ->name('pengumuman.update');
        Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy']) ->name('pengumuman.destroy');
    });

    // Pengumuman publik (untuk user yang sudah login)
    Route::get('/pengumuman-luar',      [PengumumanLuarController::class, 'index'])->name('pengumuman_luar.index');
    Route::get('/pengumuman-luar/{id}', [PengumumanLuarController::class, 'show']) ->name('pengumuman_luar.show');

    // ── Inovasi (Peserta & Admin Bapperida) ───────────────────────────────────
    Route::prefix('inovasi')->name('inovasi.')->group(function () {
    // Navigasi / daftar
    Route::get('/riwayat',     [InovasiController::class, 'riwayat'])    ->name('riwayat');
    Route::get('/rekap-nilai', [InovasiController::class, 'rekapNilai']) ->name('rekapnilai');

    // Form & riwayat per sub event
    Route::get('/usulan/{subEventId}',         [InovasiController::class, 'usulan'])        ->name('usulan');
    Route::get('/usulan-riwayat/{subEventId}', [InovasiController::class, 'usulanRiwayat']) ->name('usulan-riwayat');
    Route::get('/usulan-nilai/{subEventId}',   [InovasiController::class, 'usulanNilai'])   ->name('usulan-nilai');

    // CRUD usulan (pemilik usulan)
    Route::post('/',           [InovasiController::class, 'store'])   ->name('store');
    Route::put('/{id}',        [InovasiController::class, 'update'])  ->name('update');
    Route::delete('/{id}',     [InovasiController::class, 'destroy']) ->name('destroy');
    Route::post('/{id}/kirim', [InovasiController::class, 'kirim'])   ->name('kirim');

    // ⬇️ BARU — Edit status oleh Admin Bapperida (UC-09)
    Route::post('/{id}/edit-status', [InovasiController::class, 'editStatus'])->name('edit-status');

    Route::get('/rekap-pendaftar/{id}', [InovasiController::class, 'rekapPendaftar'])->name('rekap-pendaftar');
});

    // ── Penilaian ─────────────────────────────────────────────────────────────
    Route::prefix('penilaian')->name('penilaian.')->group(function () {
        Route::get('/tahap-1',              [PenilaianController::class, 'tahap1'])       ->name('tahap1.index');
        Route::get('/tahap-1/{id}',         [PenilaianController::class, 'tahap1Show'])   ->name('tahap1.show');
        Route::post('/tahap-1/{id}/simpan',       [PenilaianController::class, 'tahap1Simpan'])      ->name('tahap1.simpan');
        Route::post('/tahap-1/{id}/simpan-nilai', [PenilaianController::class, 'tahap1SimpanNilai'])->name('tahap1.simpan.nilai');
        Route::get('/tahap-2',                    [PenilaianController::class, 'tahap2'])             ->name('tahap2.index');
        Route::get('/tahap-2/{id}',               [PenilaianController::class, 'tahap2Show'])         ->name('tahap2.show');
        Route::post('/tahap-2/{id}/simpan-nilai', [PenilaianController::class, 'tahap2Simpan'])       ->name('tahap2.simpan.nilai');
        Route::post('/catatan/{usulanId}',  [PenilaianController::class, 'simpanCatatan'])->name('catatan.simpan');
        Route::get('/catatan/{usulanId}',   [PenilaianController::class, 'getCatatan'])->name('catatan.get');
    });

    // ── Indikator ─────────────────────────────────────────────────────────────
    Route::prefix('indikator')->name('indikator.')->group(function () {

        // Tahap 1
        Route::get('/tahap-1', [IndikatorController::class, 'tahap1'])->name('tahap1');

        Route::get('/tahap-1/{subEventId}/inovasi',         [IndikatorController::class, 'detailInovasi'])      ->name('tahap1.inovasi');
        Route::post('/tahap-1/{subEventId}/inovasi',        [IndikatorController::class, 'inovasiStore'])       ->name('tahap1.inovasi.store');
        Route::put('/tahap-1/{subEventId}/inovasi/{id}',    [IndikatorController::class, 'inovasiUpdate'])      ->name('tahap1.inovasi.update');
        Route::delete('/tahap-1/{subEventId}/inovasi/{id}', [IndikatorController::class, 'inovasiDestroy'])     ->name('tahap1.inovasi.destroy');

        Route::get('/tahap-1/{subEventId}/detail/{indikatorId}',         [IndikatorController::class, 'detailIndikator'])       ->name('tahap1.detail');
        Route::post('/tahap-1/{subEventId}/detail/{indikatorId}',        [IndikatorController::class, 'detailIndikatorStore'])  ->name('tahap1.detail.store');
        Route::put('/tahap-1/{subEventId}/detail/{indikatorId}/{id}',    [IndikatorController::class, 'detailIndikatorUpdate']) ->name('tahap1.detail.update');
        Route::delete('/tahap-1/{subEventId}/detail/{indikatorId}/{id}', [IndikatorController::class, 'detailIndikatorDestroy'])->name('tahap1.detail.destroy');

        Route::post('/tahap-1/{subEventId}/formulasi',    [IndikatorController::class, 'formulasiTahap1Store'])->name('tahap1.formulasi.store');
        Route::get('/tahap-1/{subEventId}/formulasi/get', [IndikatorController::class, 'formulasiTahap1Get'])  ->name('tahap1.formulasi.get');

        // Tahap 2
        Route::get('/tahap-2', [IndikatorController::class, 'tahap2'])->name('tahap2');

        Route::get('/tahap-2/{id}/indikator',                [IndikatorController::class, 'detailIndikator2'])      ->name('tahap2.indikator');
        Route::post('/tahap-2/{subEventId}/indikator',       [IndikatorController::class, 'indikatorTahap2Store'])  ->name('tahap2.indikator.store');
        Route::put('/tahap-2/{subEventId}/indikator/{id}',   [IndikatorController::class, 'indikatorTahap2Update']) ->name('tahap2.indikator.update');
        Route::delete('/tahap-2/{subEventId}/indikator/{id}',[IndikatorController::class, 'indikatorTahap2Destroy'])->name('tahap2.indikator.destroy');

        Route::post('/tahap-2/{subEventId}/formulasi',    [IndikatorController::class, 'formulasiTahap2Store'])->name('tahap2.formulasi.store');
        Route::get('/tahap-2/{subEventId}/formulasi/get', [IndikatorController::class, 'formulasiTahap2Get'])  ->name('tahap2.formulasi.get');
    });

});
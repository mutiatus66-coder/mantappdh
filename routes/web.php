<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PenilaiController;
use App\Http\Controllers\InovasiController;
use App\Http\Controllers\IndikatorController;

// ── Penilaian 1 ──────────────────────────────────────────
Route::get('/penilaian/tahap-1',      [Admin::class, 'penilaianTahap1'])     ->name('penilaian.tahap.1.index');
Route::get('/penilaian/tahap-1/{id}', [Admin::class, 'penilaianTahap1Show'])->name('penilaian.tahap.1.show');

// ── Penilaian 2 ──────────────────────────────────────────
Route::get('/penilaian/tahap-2',      [Admin::class, 'penilaianTahap2'])     ->name('penilaian.tahap.2.index');
Route::get('/penilaian/tahap-2/{id}', [Admin::class, 'penilaianTahap2Show'])->name('penilaian.tahap.2.show');

// ── Auth pages ──────────────────────────────────────────
Route::get('/login',          fn() => view('login'));
Route::get('/sign-in',        fn() => view('sign-in'));
Route::get('/sign-up',        fn() => view('sign-up'));
Route::get('/reset-password', fn() => view('reset-password'));
Route::get('/new-password',   fn() => view('new-password'));

// ── Dashboard ────────────────────────────────────────────
Route::get('/',      fn() => view('dashboard'));
Route::get('/index', fn() => view('index'));

// ── Sub Event ────────────────────────────────────────────
Route::get   ('/sub-event',          [Admin::class, 'index'])   ->name('admin.sub-event.index');
Route::post  ('/sub-event/store',    [Admin::class, 'store'])   ->name('admin.sub-event.store');
Route::get   ('/sub-event/{id}/edit',[Admin::class, 'edit'])    ->name('admin.sub-event.edit');
Route::put   ('/sub-event/{id}',     [Admin::class, 'update'])  ->name('admin.sub-event.update');
Route::delete('/sub-event/{id}',     [Admin::class, 'destroy']) ->name('admin.sub-event.destroy');

// ── Bidang ───────────────────────────────────────────────
Route::get   ('/bidang',          [Admin::class, 'bidang'])        ->name('admin.bidang.index');
Route::post  ('/bidang/store',    [Admin::class, 'storeBidang'])   ->name('admin.bidang.store');
Route::get   ('/bidang/{id}/edit',[Admin::class, 'editBidang'])    ->name('admin.bidang.edit');
Route::put   ('/bidang/{id}',     [Admin::class, 'updateBidang'])  ->name('admin.bidang.update');
Route::delete('/bidang/{id}',     [Admin::class, 'destroyBidang']) ->name('admin.bidang.destroy');

// ── Event ────────────────────────────────────────────────
Route::get   ('/event',      [EventController::class, 'index'])   ->name('event.index');
Route::post  ('/event',      [EventController::class, 'store'])   ->name('event.store');
Route::put   ('/event/{id}', [EventController::class, 'update'])  ->name('event.update');
Route::delete('/event/{id}', [EventController::class, 'destroy']) ->name('event.destroy');

// ── User ─────────────────────────────────────────────────
Route::get   ('/user',              [UserController::class, 'index'])    ->name('user.index');
Route::post  ('/user',              [UserController::class, 'store'])    ->name('user.store');
Route::put   ('/user/{id}',         [UserController::class, 'update'])   ->name('user.update');
Route::delete('/user/{id}',         [UserController::class, 'destroy'])  ->name('user.destroy');
Route::get   ('/user/{id}/login-as',[UserController::class, 'loginAs']) ->name('user.login-as');

// ── Penilai ──────────────────────────────────────────────
Route::get   ('/penilai',      [PenilaiController::class, 'index'])   ->name('admin.penilai.index');
Route::post  ('/penilai',      [PenilaiController::class, 'store'])   ->name('admin.penilai.store');
Route::put   ('/penilai/{id}', [PenilaiController::class, 'update'])  ->name('admin.penilai.update');
Route::delete('/penilai/{id}', [PenilaiController::class, 'destroy']) ->name('admin.penilai.destroy');

// ── Pengumuman ───────────────────────────────────────────
Route::get   ('/pengumuman',      [PengumumanController::class, 'index'])   ->name('admin.pengumuman.index');
Route::post  ('/pengumuman',      [PengumumanController::class, 'store'])   ->name('admin.pengumuman.store');
Route::put   ('/pengumuman/{id}', [PengumumanController::class, 'update'])  ->name('admin.pengumuman.update');
Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy']) ->name('admin.pengumuman.destroy');

// ── Inovasi (Riwayat & Rekap Nilai) ─────────────────────
Route::get('/inovasi/riwayat',      [InovasiController::class, 'riwayat'])->name('admin.inovasi.riwayat');
Route::get('/inovasi/rekap-nilai',  [InovasiController::class, 'rekapNilai'])->name('admin.inovasi.rekapnilai');
// ── Indikator ─────────────────────────────────────────────
Route::get   ('/indikator/tahap-1',      [IndikatorController::class, 'index'])   ->name('admin.tahap-1.index');


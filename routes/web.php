<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Bidang;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PenilaiController;

Route::get('/bidang', [Admin::class, 'bidang'])
    ->name('admin.bidang.index');

Route::post('/bidang/store', [Admin::class, 'storeBidang'])
    ->name('admin.bidang.store');

Route::get('/bidang/{id}/edit', [Admin::class, 'editBidang'])
    ->name('admin.bidang.edit');

Route::put('/bidang/{id}', [Admin::class, 'updateBidang'])
    ->name('admin.bidang.update');

Route::delete('/bidang/{id}', [Admin::class, 'destroyBidang'])
    ->name('admin.bidang.destroy');
Route::get('/sub-event', [Admin::class, 'index'])
    ->name('admin.sub-event.index');

Route::post('/sub-event/store', [Admin::class, 'store'])
    ->name('admin.sub-event.store');

Route::get('/sub-event/{id}/edit', [Admin::class, 'edit'])
    ->name('admin.sub-event.edit');

Route::put('/sub-event/{id}', [Admin::class, 'update'])
    ->name('admin.sub-event.update');

Route::delete('/sub-event/{id}', [Admin::class, 'destroy'])
    ->name('admin.sub-event.destroy');

Route::get('/event', [EventController::class, 'index'])
    ->name('event.index');
Route::post('/event', [EventController::class, 'store'])
    ->name('event.store');

Route::get('/user', [UserController::class, 'index'])
    ->name('user.index');
Route::post('/user', [UserController::class, 'store'])
    ->name('user.store');
Route::delete('/user/{id}', [UserController::class, 'destroy'])
    ->name('user.destroy');

Route::get('/penilai', [PenilaiController::class, 'index'])->name('penilai.index');
Route::post('/penilai', [PenilaiController::class, 'store'])->name('penilai.store');
Route::delete('/penilai/{id}', [PenilaiController::class, 'destroy'])->name('penilai.destroy');
Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');


Route::get('/', function () {
    return view('dashboard');
});

Route::get('/login', function () {
    return view('login');
});
Route::get('/sign-in', function () {
    return view('sign-in');
});
Route::get('/sign-up', function () {
    return view('sign-up');
});
Route::get('/reset-password', function () {
    return view('reset-password');
});
Route::get('/new-password', function () {
    return view('new-password');
});
Route::get('/index', function () {
    return view('index');
});
Route::get('/event', function () {
    return view('master.event');
});

Route::get('/user', function () {
    return view('master.user');
});
Route::get('/penilai', [PenilaiController::class, 'index'])->name('penilai.index');

Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');


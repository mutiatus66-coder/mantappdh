<?php

use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('ini adalah halaman awal');
});
Route::get('/coba_dulu', function () {
    return view('bismillah');
});

<?php

use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('home');
});
Route::get('/coba_dulu', function () {
    return view('coba_dulu');
});

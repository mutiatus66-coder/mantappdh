<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/dashboard');
});
Route::get('/home', function () {
    return view('/home');
});

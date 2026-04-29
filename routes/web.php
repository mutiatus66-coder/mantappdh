<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/login', function () {
    return view('login');
});
Route::get('/sign-in', function () {
    return view('sign-in');
});
Route::get('/admin', function () {
    return view('admin');
});
Route::get('/index', function () {
    return view('index');
});

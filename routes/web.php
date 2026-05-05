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
Route::get('/sign-up', function () {
    return view('sign-up');
});
Route::get('/reset-password', function () {
    return view('reset-password');
});
Route::get('/new-password', function () {
    return view('new-password');
});
Route::get('/admin', function () {
    return view('admin');
});
Route::get('/index', function () {
    return view('index');
});
Route::get('/event', function () {
    return view('master.event');
});
Route::get('/sub-event', function () {
    return view('master.sub-event');
});
Route::get('/bidang', function () {
    return view('master.bidang');
});
Route::get('/user', function () {
    return view('master.user');
});
Route::get('/penilaian', function () {
    return view('master.penilaian');
});
Route::get('/pengumuman', function () {
    return view('master.pengumuman');
});
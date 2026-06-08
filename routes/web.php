<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth');
});

Route::get('/login', function () {
    return view('auth');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/penyiraman', function () {
    return view('penyiraman');
});

Route::get('/riwayat', function () {
    return view('riwayat');
});

Route::get('/detail-riwayat', function () {
    return view('detail-riwayat');
});

Route::get('/profile', function () {
    return view('profile');
});

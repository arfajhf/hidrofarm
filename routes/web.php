<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PenyiramanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthApiController::class, 'register']);
Route::post('/auth/login', [AuthApiController::class, 'login']);
Route::post('/auth/one-tap', [AuthApiController::class, 'oneTapLogin']);
Route::get('/auth/me', [AuthApiController::class, 'me']);
Route::post('/auth/logout', [AuthApiController::class, 'logout']);

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

Route::get('/profile', [ProfileController::class, 'index']);
Route::post('/penyiraman/update', [PenyiramanController::class, 'updatePompa']);

Route::get('/api/beranda-data', [HomeController::class, 'berandaData']);

Route::get('/api/penyiraman-data', function () {
    return response()->json([
        'kritis' => ['Blok D', 'Blok B', 'Blok C'],
        'kurang_aman' => ['Blok A', 'Blok E'],
        'aman' => ['Blok F', 'Blok G']
    ]);
});

Route::get('/api/riwayat-data', function (\Illuminate\Http\Request $request) {
    $hari = $request->query('filter', 7);

    $data = [];
    $namaHari = ['Hari Ini', 'Kemarin', 'Jum\'at', 'Kamis', 'Rabu', 'Selasa', 'Senin'];

    for ($i = 0; $i < $hari; $i++) {
        $hariTeks = $i < count($namaHari) ? $namaHari[$i] : 'Hari ke-' . ($i + 1);

        $data[] = [
            'hari' => $hariTeks,
            'status' => 'Penyiraman Selesai',
            'blok' => 'Blok A, Blok D, Blok E',

            'icon' => '/assets/icons/penyiraman/success.svg'
        ];
    }

    return response()->json($data);
});

Route::get('/riwayat/detail', function () {
    return view('riwayat-detail');
});

Route::get('/api/riwayat-detail-data', function (\Illuminate\Http\Request $request) {
    $hari = $request->query('hari', 'Hari Ini');

    return response()->json([
        'judul_hari' => $hari,
        'catatan' => [
            ['waktu' => '07:00 WIB', 'status' => 'Penyiraman Selesai', 'blok' => 'Blok A, Blok D, Blok E'],
            ['waktu' => '08:01 WIB', 'status' => 'Penyiraman Selesai', 'blok' => 'Blok A, Blok D, Blok E'],
            ['waktu' => '09:21 WIB', 'status' => 'Penyiraman Selesai', 'blok' => 'Blok A, Blok D, Blok E'],
            ['waktu' => '11:39 WIB', 'status' => 'Penyiraman Selesai', 'blok' => 'Blok A, Blok D, Blok E'],
            ['waktu' => '14:56 WIB', 'status' => 'Penyiraman Selesai', 'blok' => 'Blok A, Blok D, Blok E'],
            ['waktu' => '16:20 WIB', 'status' => 'Penyiraman Selesai', 'blok' => 'Blok A, Blok D, Blok E'],
        ]
    ]);
});

<?php

use App\Http\Controllers\AuthApiController;
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

Route::get('/api/beranda-data', function () {
    // Ini data dummy yang formatnya udah disiapin buat nerima data IoT nanti
    return response()->json([
        'suhu' => 24,
        'kelembaban' => 45,
        'status' => 'kurang_aman', // Opsinya: 'kritis', 'kurang_aman', 'aman'
        'blok_terdampak' => ['Blok A', 'Blok B', 'Blok C']
    ]);
});

Route::get('/api/penyiraman-data', function () {
    // Data dummy simulasi sensor WEMOS D1 buat halaman Penyiraman
    return response()->json([
        'kritis' => ['Blok D', 'Blok B', 'Blok C'],
        'kurang_aman' => ['Blok A', 'Blok E'],
        'aman' => ['Blok F', 'Blok G']
    ]);
});

Route::get('/api/riwayat-data', function (\Illuminate\Http\Request $request) {
    // Tangkap angka dari filter dropdown (default 7 kalau kosong)
    $hari = $request->query('filter', 7);

    $data = [];
    $namaHari = ['Hari Ini', 'Kemarin', 'Jum\'at', 'Kamis', 'Rabu', 'Selasa', 'Senin'];

    // Bikin looping data dummy sebanyak filter yang dipilih
    for ($i = 0; $i < $hari; $i++) {
        $hariTeks = $i < count($namaHari) ? $namaHari[$i] : 'Hari ke-' . ($i + 1);

        $data[] = [
            'hari' => $hariTeks,
            'status' => 'Penyiraman Selesai',
            'blok' => 'Blok A, Blok D, Blok E',
            // Kita anggep ikon centang path-nya ini (sesuaiin sama folder lo)
            'icon' => '/assets/icons/penyiraman/success.svg'
        ];
    }

    return response()->json($data);
});

// Rute buat nampilin halaman Blade-nya
Route::get('/riwayat/detail', function () {
    return view('riwayat-detail');
});

// Rute API dummy buat ngasih data jam
Route::get('/api/riwayat-detail-data', function (\Illuminate\Http\Request $request) {
    // Tangkap nama hari dari URL, kalau kosong defaultnya 'Hari Ini'
    $hari = $request->query('hari', 'Hari Ini');

    // Bikin 6 data dummy jam penyiraman kayak di desain lo
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

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function berandaData()
    {
        // 1. Ambil data sensor
        $response = Http::get('https://siphantom.realtywire.web.id/api_datasensor.php');
        $data = $response->json();

        // 2. Baca Mode (Otomatis/Manual) dari server Siphantom
        $mode = trim(@file_get_contents('https://siphantom.realtywire.web.id/mode.txt') ?: 'auto');

        if (!$data || $data['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Sensor offline'], 500);
        }

        $sensor = $data['data'];
        $kelembaban = (float) $sensor['soil_moisture'];
        $suhu = (float) $sensor['suhu'];
        $blokList = ['Blok A', 'Blok B', 'Blok C', 'Blok D', 'Blok E'];

        // 3. LOGIKA OTOMASI (Hanya jalan jika mode 'auto')
        if ($mode === 'auto') {
            $statusTarget = ($kelembaban < 40) ? 'on' : 'off';
            Http::withHeaders(['X-API-KEY' => 'token-rahasia-hydrofarm'])
                ->post('https://siphantom.realtywire.web.id/api_pump.php', ['status' => $statusTarget]);
        }

        // 4. Status Dashboard
        $status = ($kelembaban < 40) ? 'kritis' : (($kelembaban > 60) ? 'kurang_aman' : 'aman');

        return response()->json([
            'suhu' => $suhu,
            'kelembaban' => $kelembaban,
            'status' => $status,
            'mode' => $mode,
            'blok_terdampak' => ($status === 'aman') ? [] : array_slice($blokList, 0, rand(1, 3))
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function berandaData()
    {
        // 1. Ambil data sensor
        $response = Http::timeout(3)->get('https://siphantom.realtywire.web.id/api_datasensor.php');
        $data = $response->json();

        if (!$data || !isset($data['status']) || $data['status'] !== 'success') {
            return response()->json(['status' => 'error', 'message' => 'Gagal konek sensor'], 500);
        }

        // 2. Ambil mode dari API database
        $res = Http::withHeaders(['Cache-Control' => 'no-cache'])->get('https://siphantom.realtywire.web.id/api_get_mode.php');

        $mode = $res->json()['mode'] ?? 'manual';

        $sensor = $data['data'];
        $kelembaban = (float) $sensor['soil_moisture'];

        // 3. LOGIKA OTOMATIS (Tambahkan pengecekan status saat ini biar gak spam request)
        // 3. LOGIKA OTOMATIS
        if ($mode === 'auto') {
            $statusTarget = ($kelembaban < 40) ? 'on' : 'off';

            // AMBIL STATUS RELAY SEKARANG
            // Kita cek dulu status relay yang ada di database Siphantom
            $statusRelayRes = Http::timeout(2)->get('https://siphantom.realtywire.web.id/api_get_status_relay.php');
            $statusRelay = $statusRelayRes->json()['status'] ?? 'off';

            // Hanya kirim perintah kalau status relay BEDA dengan target
            if ($statusRelay !== $statusTarget) {
                Http::withHeaders(['X-API-KEY' => 'token-rahasia-hydrofarm'])
                    ->post('https://siphantom.realtywire.web.id/api_pump.php', [
                        'status' => $statusTarget,
                    ]);
            }
        }

        $status = ($kelembaban < 40) ? 'kritis' : (($kelembaban > 60) ? 'kurang_aman' : 'aman');

        return response()->json([
            'suhu' => (float)($sensor['suhu'] ?? 0),
            'kelembaban' => $kelembaban,
            'status' => $status,
            'mode' => $mode,
            'blok_terdampak' => ($status === 'aman') ? [] : ['Blok A', 'Blok B']
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PenyiramanController extends Controller
{
    public function updatePompa(Request $request)
    {
        // Validasi input dari frontend
        $status = $request->input('status'); // 'on' atau 'off'

        // Tembak ke Siphantom (PHP Native)
        $response = Http::withHeaders([
            'X-API-KEY' => 'token-rahasia-hydrofarm' // Pastikan sama dengan di Siphantom
        // ])->post('https://siphantom.realtywire.web.id/api_pump.php', [
        ])->post('https://siphantom.realtywire.web.id/api_pump.php', [
            'status' => $status
        ]);

        return response()->json([
            'success' => $response->successful(),
            'message' => $response->json()
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    public function getUsers()
    {
        $baseUrl = config('services.service_a.base_url');
        $response = Http::get("{$baseUrl}/api/users");

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'message' => 'Gagal mengambil data dari service A'
        ], $response->status());
    }

    public function index(Request $request, $ticker)
    {
        // Validasi hanya email dan password karena ticker dari parameter URL
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $serviceABaseUrl = config('services.service_a.base_url');
        $serviceCBaseUrl = config('services.service_c.base_url');

        if (!$serviceCBaseUrl) {
            return response()->json(['message' => 'Service C base URL not configured'], 500);
        }


        // Step 1: Auth ke Service A
        $authResponse = Http::post("{$serviceABaseUrl}/api/login", [
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ]);


        $authData = $authResponse->json();


        if (!isset($authData['authenticate']) || $authData['authenticate'] !== true) {
            return response()->json(['message' => 'User not authenticated'], 403);
        }

        $stockResponse = Http::get("{$serviceCBaseUrl}/api/stock/{$ticker}");


        if (!$stockResponse->successful()) {
            return response()->json([
                'message' => 'Failed to retrieve data from Service C'
            ], $stockResponse->status());
        }

        return response()->json([
            'ticker' => $ticker,
            'data'   => $stockResponse->json()
        ]);
    }
}

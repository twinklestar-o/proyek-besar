<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AbsensiKelasController extends Controller
{
    public function getTotalKehadiran(Request $request)
    {
        // Initialize data as null
        $data = null;

        // Check if the form has been submitted
        if ($request->has('kode_mk') && $request->has('id_kur')) {
            // Validate input parameters
            $request->validate([
                'kode_mk' => 'required|string',
                'id_kur' => 'required|string',
                'start_time' => 'nullable|date',
                'end_time' => 'nullable|date',
                'minggu_ke' => 'nullable|integer',
                'day' => 'nullable|integer|min:1|max:31',
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer',
            ]);

            // Fetch API token from session
            $apiToken = session('api_token');
            if (!$apiToken) {
                $apiToken = $this->getApiToken();
            }

            if ($apiToken) {
                try {
                    // Build query parameters
                    $queryParams = array_filter([
                        'kode_mk' => $request->kode_mk,
                        'id_kur' => $request->id_kur,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'minggu_ke' => $request->minggu_ke,
                        'day' => $request->day,
                        'month' => $request->month,
                        'year' => $request->year,
                    ]);

                    // Send request to external API
                    $response = Http::withToken($apiToken)
                        ->withOptions(['verify' => false])
                        ->get('https://cis-dev.del.ac.id/api/statistik-api/get-total-kehadiran-mhs', $queryParams);

                    if ($response->successful()) {
                        $data = $response->json();
                    } else {
                        Log::warning('API request failed.', ['status' => $response->status()]);
                    }

                    Log::info('AbsensiKelas API response:', ['response' => $data]);
                } catch (\Exception $e) {
                    Log::error('Error fetching AbsensiKelas data:', ['message' => $e->getMessage()]);
                    // Do not throw exception, just log and proceed
                }
            }
        }

        // Return the view with the data (even if data is null)
        return view('app.absensi_kelas', compact('data'));
    }

    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $response = Http::withOptions(['verify' => false])
                ->post('https://cis-dev.del.ac.id/api/auth/login', [
                    'username' => 'johannes',
                    'password' => 'Del@2022',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $apiToken = $data['token'] ?? null;

                if ($apiToken) {
                    session(['api_token' => $apiToken]); // Store the token in session
                    Log::info('API login successful. Token stored.');
                    return $apiToken;
                }
            }

            Log::warning('API login failed.', ['status' => $response->status()]);
        } catch (\Exception $e) {
            Log::error('API Login Error:', ['message' => $e->getMessage()]);
        }

        Log::error('Unable to retrieve API token.');
        return null;
    }
}

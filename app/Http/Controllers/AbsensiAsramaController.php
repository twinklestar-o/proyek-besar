<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AbsensiAsramaController extends Controller
{
    public function getAbsensiAsrama(Request $request)
    {
        // Initialize data as null
        $data = null;

        // Check if the form has been submitted
        if ($request->has('id_asrama')) {
            // Validate the request parameters
            $request->validate([
                'id_asrama' => 'required|string',
                'start_time' => 'nullable|date',
                'end_time' => 'nullable|date',
                'day' => 'nullable|integer|min:1|max:31',
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:2000|max:2099',
            ]);

            // Get the API token from the session
            $apiToken = session('api_token');
            if (!$apiToken) {
                $apiToken = $this->getApiToken();
            }

            if ($apiToken) {
                try {
                    // Build the query parameters
                    $queryParams = array_filter([
                        'id_asrama' => $request->id_asrama,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'day' => $request->day,
                        'month' => $request->month,
                        'year' => $request->year,
                    ]);

                    // Send the request to the external API
                    $response = Http::withToken($apiToken)
                        ->withOptions(['verify' => false])
                        ->get('https://cis-dev.del.ac.id/api/statistik-api/get-total-absensi-by-asrama', $queryParams);

                    if ($response->successful()) {
                        $data = $response->json();
                    } else {
                        Log::warning('API request failed.', ['status' => $response->status()]);
                    }

                    Log::info('Absensi Asrama API response:', ['response' => $data]);
                } catch (\Exception $e) {
                    Log::error('Error fetching Absensi Asrama data:', ['message' => $e->getMessage()]);
                    // Do not throw exception, just log and proceed
                }
            }
        }

        // Return the view with the data (even if data is null)
        return view('app.absensi_asrama', compact('data'));
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LogController extends Controller
{
    public function getLogMahasiswa(Request $request)
    {
        $data = null;

        // Check if any filter parameter is provided
        if ($request->hasAny(['start_masuk', 'end_masuk', 'start_keluar', 'end_keluar', 'day', 'month', 'year'])) {
            // Validate the request parameters
            $request->validate([
                'start_masuk' => 'nullable|date',
                'end_masuk' => 'nullable|date',
                'start_keluar' => 'nullable|date',
                'end_keluar' => 'nullable|date',
                'day' => 'nullable|string',
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:2000|max:2099',
            ]);

            $apiToken = $this->getValidApiToken();

            if ($apiToken) {
                try {
                    // Build the query parameters
                    $queryParams = array_filter([
                        'start_masuk' => $request->start_masuk,
                        'end_masuk' => $request->end_masuk,
                        'start_keluar' => $request->start_keluar,
                        'end_keluar' => $request->end_keluar,
                        'day' => $request->day,
                        'month' => $request->month,
                        'year' => $request->year,
                    ]);

                    // Build cache key based on query parameters
                    $cacheKey = 'logMahasiswa_' . md5(json_encode($queryParams));

                    $data = Cache::remember($cacheKey, 300, function () use ($apiToken, $queryParams) {
                        $response = Http::withToken($apiToken)
                            ->withOptions(['verify' => false, 'timeout' => 5])
                            ->get('https://cis-dev.del.ac.id/api/statistik-api/get-total-log-mhs', $queryParams);

                        if ($response->successful()) {
                            $data = $response->json();
                            return $data;
                        } else {
                            Log::warning('API request failed.', ['status' => $response->status()]);
                        }
                        return null;
                    });

                    Log::info('Log Mahasiswa API response:', ['response' => $data]);
                } catch (\Exception $e) {
                    Log::error('Error fetching Log Mahasiswa data:', ['message' => $e->getMessage()]);
                    // Do not throw exception, just log and proceed
                }
            }
        }

        // Return the view with the data (even if data is null)
        return view('app.log', compact('data'));
    }

    protected function getValidApiToken()
    {
        $apiToken = session('api_token');
        $tokenObtainedAt = session('api_token_obtained_at');

        // Check if token is older than 55 minutes
        if (!$apiToken || !$tokenObtainedAt || now()->diffInMinutes($tokenObtainedAt) >= 55) {
            $apiToken = $this->getApiToken();
        }

        return $apiToken;
    }

    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $response = Http::withOptions(['verify' => false, 'timeout' => 5])
                ->post('https://cis-dev.del.ac.id/api/auth/login', [
                    'username' => 'johannes',
                    'password' => 'Del@2022',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $apiToken = $data['token'] ?? null;

                if ($apiToken) {
                    session([
                        'api_token' => $apiToken,
                        'api_token_obtained_at' => now(),
                    ]);
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

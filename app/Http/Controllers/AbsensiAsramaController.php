<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class AbsensiAsramaController extends Controller
{
    public function getAbsensiAsrama(Request $request)
    {
        $data = null;

        // Validate and filter input
        if ($request->has('id_asrama')) {
            $request->validate([
                'id_asrama' => 'required|string',
                'start_time' => 'nullable|date',
                'end_time' => 'nullable|date',
                'day' => 'nullable|integer|min:1|max:31',
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:2000|max:2099',
            ]);

            // Retrieve or fetch API token
            $apiToken = session('api_token') ?? $this->getApiToken();

            if (!$apiToken) {
                Log::error('Failed to obtain API token.');
                return view('app.absensi_asrama', ['data' => null, 'errors' => ['Unable to authenticate with the API.']]);
            }

            try {
                // Build query parameters
                $queryParams = array_filter([
                    'id_asrama' => $request->id_asrama,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'day' => $request->day,
                    'month' => $request->month,
                    'year' => $request->year,
                ]);

                // Cache key based on query parameters
                $cacheKey = 'absensiAsrama_' . md5(json_encode($queryParams));

                // Fetch data from cache or API
                $data = Cache::remember($cacheKey, 300, function () use ($apiToken, $queryParams) {
                    $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);

                    $response = $client->get('https://cis-dev.del.ac.id/api/statistik-api/get-total-absensi-by-asrama', [
                        'headers' => [
                            'Authorization' => "Bearer $apiToken",
                        ],
                        'query' => $queryParams,
                    ]);

                    if ($response->getStatusCode() === 200) {
                        return json_decode($response->getBody(), true);
                    }

                    Log::warning('Unexpected response from API.', ['status' => $response->getStatusCode()]);
                    return null;
                });

                Log::info('Absensi Asrama API response:', ['response' => $data]);
            } catch (\Exception $e) {
                if ($e->getCode() === 401) {
                    Log::warning('Token expired, attempting to refresh...');
                    session()->forget('api_token');
                    $apiToken = $this->getApiToken();

                    if ($apiToken) {
                        return $this->getAbsensiAsrama($request);
                    }
                }

                Log::error('Error fetching Absensi Asrama data:', ['message' => $e->getMessage()]);
            }
        }

        return view('app.absensi_asrama', compact('data'));
    }

    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);

            $response = $client->post('https://cis-dev.del.ac.id/api/auth/login', [
                'form_params' => [
                    'username' => 'johannes',
                    'password' => 'Del@2022',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                $apiToken = $data['token'] ?? null;

                if ($apiToken) {
                    session(['api_token' => $apiToken]);
                    Log::info('API login successful. Token stored in session.', ['token' => $apiToken]);
                    return $apiToken;
                }
            }

            Log::error('API login failed.', ['response' => $response->getBody()->getContents()]);
        } catch (\Exception $e) {
            Log::error('Error during API login:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

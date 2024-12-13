<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class PelanggaranController extends Controller
{
    public function getPelanggaranByAsrama(Request $request)
    {
        $data = null;

        // Check if any filter parameter is provided
        if ($request->hasAny(['id_asrama', 'tingkat_pelanggaran', 'start_date', 'end_date', 'day', 'month', 'year'])) {
            // Validate the request parameters
            $request->validate([
                'id_asrama' => 'nullable|string',
                'tingkat_pelanggaran' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'day' => 'nullable|string',
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:2000|max:2099',
            ]);

            $apiToken = session('api_token') ?? $this->getApiToken();

            if (!$apiToken) {
                Log::error('Failed to obtain API token.');
                $errors = ['Unable to authenticate with the API. Please try again later.'];
                return view('app.pelanggaran', compact('data', 'errors'));
            }

            try {
                // Build the form parameters
                $formParams = array_filter([
                    'id_asrama' => $request->id_asrama,
                    'tingkat_pelanggaran' => $request->tingkat_pelanggaran,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'day' => $request->day,
                    'month' => $request->month,
                    'year' => $request->year,
                ]);

                // Build cache key based on form parameters
                $cacheKey = 'pelanggaranByAsrama_' . md5(json_encode($formParams));

                $data = Cache::remember($cacheKey, 300, function () use ($apiToken, $formParams) {
                    $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);

                    $response = $client->post('https://cis-dev.del.ac.id/api/statistik-api/get-total-pelanggaran-by-asrama', [
                        'headers' => [
                            'Authorization' => "Bearer $apiToken",
                            'Accept' => 'application/json',
                        ],
                        'query' => $formParams, // Sending form data
                    ]);

                    if ($response->getStatusCode() === 200) {
                        $data = json_decode($response->getBody(), true);
                        return $data;
                    }

                    Log::warning('Unexpected response from API.', ['status' => $response->getStatusCode()]);
                    return null;
                });

                Log::info('Pelanggaran API response:', ['response' => $data]);
            } catch (\Exception $e) {
                if ($e->getCode() === 401) {
                    Log::warning('Token expired, attempting to refresh...');
                    session()->forget('api_token');
                    $apiToken = $this->getApiToken();

                    if ($apiToken) {
                        return $this->getPelanggaranByAsrama($request);
                    }
                }

                Log::error('Error fetching Pelanggaran data:', ['message' => $e->getMessage()]);
            }
        }

        // Return the view with the data (even if data is null)
        $errors = ['Unable to fetch data from the API. Please try again later.'];
        return view('app.pelanggaran', compact('data', 'errors'));
    }

    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);

            $response = $client->post('https://cis-dev.del.ac.id/api/jwt-api/do-auth', [
                'form_params' => [
                    'username' => 'johannes',
                    'password' => 'Del@2022',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if ($response->getStatusCode() === 200 && isset($data['token'])) {
                // Store the token in the session
                session([
                    'api_token' => $data['token'],
                    'api_token_obtained_at' => now(),
                ]);
                Log::info('API login successful, token stored in session.', ['token' => $data['token']]);
                return $data['token'];
            }

            Log::error('API login failed.', ['response' => $data]);
        } catch (\Exception $e) {
            Log::error('Error during API login:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

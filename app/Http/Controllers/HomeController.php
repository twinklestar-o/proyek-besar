<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected $apiLoginRetries = 3; // Define threshold for API login attempts

    public function index()
    {
        $apiToken = session('api_token');

        if (!$apiToken) {
            Log::warning('API token not found. Attempting to retrieve a new token...');
            $apiToken = $this->getApiToken();
        }

        if ($apiToken) {
            try {
                Log::info('Fetching total active students from external API...');
                $response = Http::withToken($apiToken)
                    ->withOptions(['verify' => false])
                    ->get('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                        'angkatan' => '',
                        'prodi' => '',
                    ]);

                Log::info('API Response', ['status' => $response->status(), 'body' => $response->body()]);

                if ($response->successful()) {
                    $data = $response->json();
                    $totalMahasiswaAktif = $data['total'] ?? null;

                    return view('app.client.home', compact('totalMahasiswaAktif'));
                } else {
                    Log::error('Failed to fetch data from API.', ['status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::error('API Error:', ['message' => $e->getMessage()]);
            }
        }

        // If API token or request fails, proceed with the view
        return view('app.client.home', ['totalMahasiswaAktif' => null]);
    }

    protected function getApiToken()
    {
        $retries = 0;
        while ($retries < $this->apiLoginRetries) {
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

                Log::warning('API login failed. Retrying...', ['status' => $response->status()]);
            } catch (\Exception $e) {
                Log::error('API Login Error:', ['message' => $e->getMessage()]);
            }

            $retries++;
        }

        Log::error('API login failed after maximum retries.');
        return null;
    }
}

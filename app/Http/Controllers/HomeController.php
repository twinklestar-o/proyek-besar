<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $apiToken = $this->getValidApiToken();
        $angkatan = $request->get('angkatan', '');
        $prodi = $request->get('prodi', '');

        $totalMahasiswaAktif = null;
        $prodiList = ['Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Manajemen Rekayasa'];

        if ($apiToken) {
            try {
                // Build cache key based on filters
                $cacheKey = 'totalMahasiswaAktif_' . md5($angkatan . '_' . $prodi);

                // Try to get data from cache
                $totalMahasiswaAktif = Cache::remember($cacheKey, 300, function () use ($apiToken, $angkatan, $prodi) {
                    $response = Http::withToken($apiToken)
                        ->withOptions(['verify' => false, 'timeout' => 5])
                        ->get('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                            'angkatan' => $angkatan,
                            'prodi' => $prodi,
                        ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        return $data['total'] ?? null;
                    }
                    return null;
                });
            } catch (\Exception $e) {
                Log::error('API Error:', ['message' => $e->getMessage()]);
            }
        }

        return view('app.home', compact('totalMahasiswaAktif', 'prodiList'));
    }

    protected function getValidApiToken()
    {
        try {
            Log::info('Attempting API login directly in getValidApiToken...');
            $response = Http::withOptions(['verify' => false, 'timeout' => 5])
                ->post('https://cis-dev.del.ac.id/api/auth/login', [
                    'username' => 'johannes',
                    'password' => 'Del@2022',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $apiToken = $data['token'] ?? null;

                if ($apiToken) {
                    Log::info('API login successful in getValidApiToken.');
                    return $apiToken;
                }
            }

            Log::warning('API login failed in getValidApiToken.', ['status' => $response->status()]);
        } catch (\Exception $e) {
            Log::error('Error while fetching API token in getValidApiToken:', ['message' => $e->getMessage()]);
        }

        return null;
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

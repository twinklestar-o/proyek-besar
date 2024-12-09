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
        Log::info('HomeController@index called.');

        $apiToken = $this->getValidApiToken();
        Log::info('API Token retrieved:', ['token' => $apiToken]);

        $angkatan = $request->get('angkatan', '');
        $prodi = $request->get('prodi', '');

        $totalMahasiswaAktif = null;
        $prodiList = ['Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Manajemen Rekayasa'];

        if ($apiToken) {
            try {
                // Build cache key based on filters
                $cacheKey = 'totalMahasiswaAktif_' . md5($angkatan . '_' . $prodi);
                Log::info('Cache Key:', ['cacheKey' => $cacheKey]);

                // Try to get data from cache
                $totalMahasiswaAktif = Cache::remember($cacheKey, 300, function () use ($apiToken, $angkatan, $prodi) {
                    Log::info('Making API request to get-total-mahasiswa-aktif.');

                    $response = Http::withToken($apiToken)
                        ->withOptions(['verify' => false, 'timeout' => 5])
                        ->get('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                            'angkatan' => $angkatan,
                            'prodi' => $prodi,
                        ]);

                    Log::info('API Response Status:', ['status' => $response->status()]);
                    Log::info('API Response Body:', ['body' => $response->body()]);

                    if ($response->successful()) {
                        $data = $response->json();
                        Log::info('API Response Data:', ['data' => $data]);
                        return $data['total'] ?? null;
                    } else {
                        Log::warning('API request failed.', [
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                    }
                    return null;
                });
            } catch (\Exception $e) {
                Log::error('Exception while fetching totalMahasiswaAktif:', ['message' => $e->getMessage()]);
            }
        } else {
            Log::warning('No API token available.');
        }

        return view('app.home', compact('totalMahasiswaAktif', 'prodiList'));
    }

    protected function getValidApiToken()
    {
        try {
            Log::info('Attempting API login directly in getValidApiToken...');
            $response = Http::withOptions(['verify' => false, 'timeout' => 5])
                ->post('https://cis-dev.del.ac.id/api/jwt-api/do-auth', [
                    'username' => 'johannes',
                    'password' => 'Del@2022',
                ]);

            Log::info('API Login Response Status:', ['status' => $response->status()]);
            Log::info('API Login Response Body:', ['body' => $response->body()]);

            if ($response->successful()) {
                $data = $response->json();
                $apiToken = $data['token'] ?? null;

                if ($apiToken) {
                    Log::info('API login successful in getValidApiToken.');
                    return $apiToken;
                } else {
                    Log::warning('API token not found in the response.');
                }
            } else {
                Log::warning('API login failed in getValidApiToken.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error while fetching API token in getValidApiToken:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

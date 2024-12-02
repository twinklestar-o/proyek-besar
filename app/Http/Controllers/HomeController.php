<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $apiToken = session('api_token');
        $angkatan = $request->get('angkatan', ''); // Get angkatan filter
        $prodi = $request->get('prodi', '');       // Get prodi filter

        if (!$apiToken) {
            $apiToken = $this->getApiToken();
        }

        $totalMahasiswaAktif = null;
        $prodiList = ['Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Manajemen Rekayasa']; // Example list, replace with actual data

        if ($apiToken) {
            try {
                $response = Http::withToken($apiToken)
                    ->withOptions(['verify' => false])
                    ->get('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                        'angkatan' => $angkatan,
                        'prodi' => $prodi,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $totalMahasiswaAktif = $data['total'] ?? null;
                }
            } catch (\Exception $e) {
                Log::error('API Error:', ['message' => $e->getMessage()]);
            }
        }

        return view('app.home', compact('totalMahasiswaAktif', 'prodiList'));
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

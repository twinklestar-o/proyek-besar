<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $angkatan = $request->get('angkatan', '');
        $prodi = $request->get('prodi', '');
        $totalMahasiswaAktif = null;
        $prodiList = ['Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Manajemen Rekayasa'];

        // Check if API token exists in session
        $apiToken = session('api_token') ?? $this->getApiToken();

        if ($apiToken) {
            try {
                $cacheKey = 'totalMahasiswaAktif_' . md5($angkatan . '_' . $prodi);

                $totalMahasiswaAktif = Cache::remember($cacheKey, 300, function () use ($apiToken, $angkatan, $prodi) {
                    $client = new Client(['verify' => false, 'timeout' => 10]);

                    $response = $client->get('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                        'headers' => [
                            'Authorization' => "Bearer $apiToken",
                        ],
                        'query' => [
                            'angkatan' => $angkatan,
                            'prodi' => $prodi,
                        ],
                    ]);

                    if ($response->getStatusCode() === 200) {
                        $data = json_decode($response->getBody(), true);
                        return $data['total'] ?? null;
                    }

                    return null;
                });
            } catch (\Exception $e) {
                Log::error('Error fetching data:', ['message' => $e->getMessage()]);
            }
        } else {
            Log::error('Failed to obtain API token.');
        }

        return view('app.home', compact('totalMahasiswaAktif', 'prodiList'));
    }

    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $client = new Client(['verify' => false, 'timeout' => 10]);

            $response = $client->post('https://cis-dev.del.ac.id/api/jwt-api/do-auth', [
                'form_params' => [ // Sending form-data
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
                session(['api_token' => $data['token']]);
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

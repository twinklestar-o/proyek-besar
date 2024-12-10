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

        // Updated prodiList with IDs and names
        $prodiList = [
            1 => 'D3 Teknologi Informasi',
            3 => 'D3 Teknologi Komputer',
            4 => 'STr Teknologi Rekayasa Perangkat Lunak',
            6 => 'S1 Informatika',
            7 => 'S1 Teknik Elektro',
            8 => 'S1 Teknik Bioproses',
            9 => 'S1 Sistem Informasi',
            10 => 'S1 Manajemen Rekayasa',
            16 => 'S1 Teknik Metalurgi',
        ];

        // Retrieve the API token from session or fetch a new one
        $apiToken = session('api_token') ?? $this->getApiToken();

        if (!$apiToken) {
            Log::error('Failed to obtain API token.');
            $errors = ['Unable to authenticate with the API. Please try again later.'];
            return view('app.home', compact('totalMahasiswaAktif', 'prodiList', 'errors'));
        }

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

                Log::warning('Unexpected response from API.', ['status' => $response->getStatusCode()]);
                return null;
            });
        } catch (\Exception $e) {
            if ($e->getCode() === 401) {
                Log::warning('Token expired, attempting to refresh...');
                session()->forget('api_token');
                $apiToken = $this->getApiToken();

                if ($apiToken) {
                    return $this->index($request);
                }
            }

            Log::error('Error fetching data from API:', ['message' => $e->getMessage()]);
        }

        $errors = ['Unable to fetch data from the API. Please try again later.'];
        return view('app.home', compact('totalMahasiswaAktif', 'prodiList', 'errors'));
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

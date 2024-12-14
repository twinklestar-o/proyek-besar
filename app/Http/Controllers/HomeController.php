<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Ambil dari request atau session untuk mempertahankan filter terakhir
        $angkatan = $request->has('angkatan') ? $request->get('angkatan', '') : session('last_angkatan', '');
        $prodi = $request->has('prodi') ? $request->get('prodi', '') : session('last_prodi', '');

        // Simpan pilihan terakhir ke session
        session([
            'last_angkatan' => $angkatan,
            'last_prodi' => $prodi
        ]);

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

        $apiToken = session('api_token') ?? $this->getApiToken();

        $dataMahasiswa = [];
        $errors = [];

        if (!$apiToken) {
            Log::error('Failed to obtain API token.');
            $errors = ['Unable to authenticate with the API. Please try again later.'];
            return view('app.home', compact('dataMahasiswa', 'prodiList', 'angkatan', 'prodi', 'errors'));
        }

        try {
            $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 60]);

            // Buat cache key unik berdasarkan filter
            $cacheKey = 'dataMahasiswa_' . md5(json_encode(['angkatan' => $angkatan, 'prodi' => $prodi]));

            if (Cache::has($cacheKey)) {
                // Ambil data dari cache
                $dataMahasiswa = Cache::get($cacheKey);
            } else {
                // Data belum di-cache, lakukan request sesuai kondisi
                if (empty($angkatan) && empty($prodi)) {
                    // Semua Prodi, Semua Angkatan
                    $promises = [];
                    foreach ($prodiList as $prodiId => $prodiName) {
                        $promises[$prodiName] = $client->getAsync('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                            'headers' => [
                                'Authorization' => "Bearer $apiToken",
                                'Accept' => 'application/json',
                            ],
                            'query' => [
                                'angkatan' => '',
                                'prodi' => $prodiId,
                            ],
                        ]);
                    }

                    $responses = Utils::settle($promises)->wait();
                    $grandTotal = 0;
                    foreach ($responses as $prodiName => $result) {
                        if ($result['state'] === 'fulfilled') {
                            $data = json_decode($result['value']->getBody()->getContents(), true);
                            $value = $data['total'] ?? 0;
                            $dataMahasiswa[$prodiName] = (int) $value;
                            $grandTotal += (int) $value;
                        } else {
                            $dataMahasiswa[$prodiName] = 0;
                        }
                    }
                    $dataMahasiswa['total'] = $grandTotal;

                } elseif (empty($angkatan) && !empty($prodi)) {
                    // Semua angkatan tapi prodi terisi
                    $currentYear = date('Y');
                    $angkatanRange = range($currentYear - 6, $currentYear);
                    $promises = [];
                    foreach ($angkatanRange as $year) {
                        $promises[$year] = $client->getAsync('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                            'headers' => [
                                'Authorization' => "Bearer $apiToken",
                                'Accept' => 'application/json',
                            ],
                            'query' => [
                                'angkatan' => $year,
                                'prodi' => $prodi,
                            ],
                        ]);
                    }

                    $responses = Utils::settle($promises)->wait();
                    foreach ($responses as $year => $result) {
                        if ($result['state'] === 'fulfilled') {
                            $data = json_decode($result['value']->getBody()->getContents(), true);
                            $dataMahasiswa[$year] = $data['total'] ?? 0;
                        } else {
                            $dataMahasiswa[$year] = 0;
                        }
                    }

                } elseif (!empty($angkatan) && empty($prodi)) {
                    // Semua prodi tapi angkatan diisi
                    $promises = [];
                    foreach ($prodiList as $prodiId => $prodiName) {
                        $promises[$prodiName] = $client->getAsync('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                            'headers' => [
                                'Authorization' => "Bearer $apiToken",
                                'Accept' => 'application/json',
                            ],
                            'query' => [
                                'angkatan' => $angkatan,
                                'prodi' => $prodiId,
                            ],
                        ]);
                    }

                    $responses = Utils::settle($promises)->wait();
                    foreach ($responses as $prodiName => $result) {
                        if ($result['state'] === 'fulfilled') {
                            $data = json_decode($result['value']->getBody()->getContents(), true);
                            $dataMahasiswa[$prodiName] = $data['total'] ?? 0;
                        } else {
                            $dataMahasiswa[$prodiName] = 0;
                        }
                    }

                } else {
                    // Kedua parameter terisi (single request)
                    $response = $client->get('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                        'headers' => [
                            'Authorization' => "Bearer $apiToken",
                            'Accept' => 'application/json',
                        ],
                        'query' => [
                            'angkatan' => $angkatan,
                            'prodi' => $prodi,
                        ],
                    ]);

                    $data = json_decode($response->getBody()->getContents(), true);
                    if (isset($data['total'])) {
                        $dataMahasiswa = ['total' => (int) $data['total']];
                    } else {
                        $dataMahasiswa = ['message' => 'Data tidak ditemukan.'];
                    }
                }

                // Simpan hasil ke cache selama 5 menit (300 detik)
                Cache::put($cacheKey, $dataMahasiswa, 300);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching data from API:', ['message' => $e->getMessage()]);
            $dataMahasiswa = ['message' => 'Terjadi kesalahan saat menghubungi API.'];
        }

        return view('app.home', compact('dataMahasiswa', 'prodiList', 'angkatan', 'prodi'));
    }

    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 60]);

            $response = $client->post('https://cis-dev.del.ac.id/api/jwt-api/do-auth', [
                'form_params' => [
                    'username' => 'johannes',
                    'password' => 'Del@2022',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['token'])) {
                session(['api_token' => $data['token']]);
                Log::info('New API token retrieved:', ['token' => $data['token']]);
                return $data['token'];
            }

            Log::error('Failed to retrieve API token.', ['response' => $data]);
        } catch (\Exception $e) {
            Log::error('Error during API token retrieval:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

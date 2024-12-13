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
        $angkatan = $request->get('angkatan', ''); // Tetapkan string kosong jika tidak ada
        $prodi = $request->get('prodi', '');       // Tetapkan string kosong jika tidak ada
        $dataMahasiswa = [];
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

        if (!$apiToken) {
            Log::error('Failed to obtain API token.');
            $errors = ['Unable to authenticate with the API. Please try again later.'];
            return view('app.home', compact('dataMahasiswa', 'prodiList', 'angkatan', 'prodi', 'errors'));
        }

        try {
            $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 60]);

            if (empty($angkatan) && !empty($prodi)) {
                // Semua angkatan tapi prodi terisi
                $dataMahasiswa = $this->fetchDataByAngkatan($client, $apiToken, $prodi);
            } elseif (!empty($angkatan) && empty($prodi)) {
                // Semua prodi tapi angkatan diisi
                $dataMahasiswa = $this->fetchDataByProdi($client, $apiToken, $angkatan, $prodiList);
            } else {
                // Jika $angkatan dan $prodi keduanya kosong (Semua Prodi, Semua Angkatan)
                $dataMahasiswa = [];
                $grandTotal = 0;

                foreach ($prodiList as $prodiId => $prodiName) {
                    $response = $client->get('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                        'headers' => [
                            'Authorization' => "Bearer $apiToken",
                            'Accept' => 'application/json',
                        ],
                        'query' => [
                            'angkatan' => '',
                            'prodi' => $prodiId,
                        ],
                    ]);

                    $data = json_decode($response->getBody()->getContents(), true);

                    if (isset($data['total'])) {
                        $dataMahasiswa[$prodiName] = (int) $data['total'];
                        $grandTotal += (int) $data['total'];
                    } else {
                        $dataMahasiswa[$prodiName] = 0;
                    }
                }

                // Tambahkan total keseluruhan
                $dataMahasiswa['total'] = $grandTotal;
            }

        } catch (\Exception $e) {
            Log::error('Error fetching data from API:', ['message' => $e->getMessage()]);
            $dataMahasiswa = ['message' => 'Terjadi kesalahan saat menghubungi API.'];
        }

        return view('app.home', compact('dataMahasiswa', 'prodiList', 'angkatan', 'prodi'));
    }

    protected function fetchDataByAngkatan($client, $apiToken, $prodi)
    {
        $currentYear = date('Y');
        $angkatanRange = range($currentYear - 6, $currentYear); // Rentang 7 tahun terakhir
        $dataMahasiswa = [];

        foreach ($angkatanRange as $angkatan) {
            try {
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
                    $dataMahasiswa[$angkatan] = (int) $data['total'];
                } else {
                    Log::warning("No 'total' field in response for angkatan {$angkatan}.");
                }
            } catch (\Exception $e) {
                Log::error("Error fetching data for angkatan {$angkatan}:", ['message' => $e->getMessage()]);
            }
        }

        return $dataMahasiswa;
    }



    protected function fetchDataByProdi($client, $apiToken, $angkatan, $prodiList)
    {
        $dataMahasiswa = [];

        foreach ($prodiList as $prodiId => $prodiName) {
            try {
                $response = $client->get('https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', [
                    'headers' => [
                        'Authorization' => "Bearer $apiToken",
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'angkatan' => $angkatan,
                        'prodi' => $prodiId,
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['total'])) {
                    $dataMahasiswa[$prodiName] = (int) $data['total'];
                } else {
                    Log::warning("No 'total' field in response for prodi {$prodiName}.");
                }
            } catch (\Exception $e) {
                Log::error("Error fetching data for prodi {$prodiName}:", ['message' => $e->getMessage()]);
            }
        }

        return $dataMahasiswa;
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

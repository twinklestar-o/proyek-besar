<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use App\Models\Section;

class PelanggaranController extends Controller
{
    public function getPelanggaranByAsrama(Request $request)
    {
        $data = null;
        $errors = ['Unable to fetch data from the API. Please try again later.'];

        $tingkatPelanggaranLabels = [
            1 => 'Ringan Level I (Poin 1-5)',
            2 => 'Ringan Level II (Poin 6-10)',
            3 => 'Sedang Level I (Poin 11-15)',
            4 => 'Sedang Level II (Poin 16-24)',
            5 => 'Berat Level I (Poin 25-30)',
            6 => 'Berat Level II (Poin 31-75)',
            7 => 'Berat Level III (Poin >=76)',
        ];

        // Ambil filter dari request atau session
        $filters = [
            'id_asrama' => $request->has('id_asrama') ? $request->id_asrama : session('id_asrama'),
            'tingkat_pelanggaran' => $request->has('tingkat_pelanggaran') ? $request->tingkat_pelanggaran : session('tingkat_pelanggaran'),
            'start_date' => $request->has('start_date') ? $request->start_date : session('start_date'),
            'end_date' => $request->has('end_date') ? $request->end_date : session('end_date'),
            'day' => $request->has('day') ? $request->day : session('day'),
            'month' => $request->has('month') ? $request->month : session('month'),
            'year' => $request->has('year') ? $request->year : session('year'),
        ];

        $sections = Section::all()->keyBy('section');

        // Simpan filter ke dalam session
        session($filters);

        // Cek apakah ada salah satu parameter filter diisi
        if (array_filter($filters)) {
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
                return view('app.pelanggaran', compact('data', 'sections', 'errors', 'tingkatPelanggaranLabels', 'filters'));
            }

            try {
                $formParams = array_filter([
                    'id_asrama' => $filters['id_asrama'],
                    'start_date' => $filters['start_date'],
                    'end_date' => $filters['end_date'],
                    'day' => $filters['day'],
                    'month' => $filters['month'],
                    'year' => $filters['year'],
                ]);

                if (empty($filters['tingkat_pelanggaran'])) {
                    $levels = [1, 2, 3, 4, 5, 6, 7];
                    $dataPerLevel = [];
                    $namaAsrama = null;
                    $totalKeseluruhan = 0;

                    $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);

                    $promises = [];

                    foreach ($levels as $level) {
                        $paramsWithLevel = array_merge($formParams, ['tingkat_pelanggaran' => $level]);
                        $cacheKey = 'pelanggaranByAsrama_' . md5(json_encode($paramsWithLevel));

                        if (Cache::has($cacheKey)) {
                            $levelData = Cache::get($cacheKey);
                            $dataPerLevel[$level] = $levelData['data']['total_pelanggaran'] ?? 0;
                            $namaAsrama = $levelData['data']['nama_asrama'] ?? $namaAsrama;
                            $totalKeseluruhan += $dataPerLevel[$level];
                        } else {
                            $promises[$level] = $client->postAsync('https://cis-dev.del.ac.id/api/statistik-api/get-total-pelanggaran-by-asrama', [
                                'headers' => [
                                    'Authorization' => "Bearer $apiToken",
                                    'Accept' => 'application/json',
                                ],
                                'query' => $paramsWithLevel,
                            ]);
                        }
                    }

                    $responses = Promise\Utils::settle($promises)->wait();

                    foreach ($responses as $level => $response) {
                        if ($response['state'] === 'fulfilled') {
                            $res = $response['value'];
                            if ($res->getStatusCode() === 200) {
                                $levelData = json_decode($res->getBody(), true);
                                if (isset($levelData['result']) && $levelData['result'] == 'OK' && isset($levelData['data'])) {
                                    $totalPelanggaran = $levelData['data']['total_pelanggaran'] ?? 0;
                                    $dataPerLevel[$level] = $totalPelanggaran;
                                    $namaAsrama = $levelData['data']['nama_asrama'] ?? $namaAsrama;
                                    $totalKeseluruhan += $totalPelanggaran;
                                    $cacheKey = 'pelanggaranByAsrama_' . md5(json_encode(array_merge($formParams, ['tingkat_pelanggaran' => $level])));
                                    Cache::put($cacheKey, $levelData, 300);
                                } else {
                                    $dataPerLevel[$level] = 0;
                                }
                            } else {
                                Log::warning('Unexpected response from API.', ['status' => $res->getStatusCode()]);
                                $dataPerLevel[$level] = 0;
                            }
                        } else {
                            if ($response['reason']->getCode() === 401) {
                                Log::warning('Token expired, attempting to refresh...');
                                session()->forget('api_token');
                                $apiToken = $this->getApiToken();
                                if (!$apiToken) {
                                    throw new \Exception('Failed to refresh API token.');
                                }
                            }
                            Log::error('Error in API request:', ['reason' => $response['reason']]);
                            $dataPerLevel[$level] = 0;
                        }
                    }

                    foreach ($levels as $level) {
                        if (!isset($dataPerLevel[$level])) {
                            $dataPerLevel[$level] = 0;
                        }
                    }

                    $data = [
                        'result' => 'OK',
                        'data' => [
                            'nama_asrama' => $namaAsrama ?? '-',
                            'total_keseluruhan' => $totalKeseluruhan,
                            'pelanggaran_per_level' => $dataPerLevel,
                        ]
                    ];
                } else {
                    $paramsWithLevel = array_merge($formParams, [
                        'tingkat_pelanggaran' => $filters['tingkat_pelanggaran'],
                    ]);

                    $cacheKey = 'pelanggaranByAsrama_' . md5(json_encode($paramsWithLevel));

                    if (Cache::has($cacheKey)) {
                        $data = Cache::get($cacheKey);
                    } else {
                        $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);
                        $response = $client->post('https://cis-dev.del.ac.id/api/statistik-api/get-total-pelanggaran-by-asrama', [
                            'headers' => [
                                'Authorization' => "Bearer $apiToken",
                                'Accept' => 'application/json',
                            ],
                            'query' => $paramsWithLevel,
                        ]);

                        if ($response->getStatusCode() === 200) {
                            $data = json_decode($response->getBody(), true);
                            Cache::put($cacheKey, $data, 300);
                        } else {
                            if ($response->getStatusCode() === 401) {
                                session()->forget('api_token');
                                $apiToken = $this->getApiToken();
                                if (!$apiToken) {
                                    throw new \Exception('Failed to refresh API token.');
                                }
                            }
                            Log::warning('Unexpected response from API.', ['status' => $response->getStatusCode()]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error fetching Pelanggaran data:', ['message' => $e->getMessage()]);
            }
        }

        return view('app.pelanggaran', compact('data', 'sections', 'errors', 'tingkatPelanggaranLabels', 'filters'));
    }

    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $client = new Client(['verify' => false, 'timeout' => 60, 'http_errors' => false]);

            $response = $client->post('https://cis-dev.del.ac.id/api/jwt-api/do-auth', [
                'form_params' => [
                    'username' => 'johannes',
                    'password' => 'Del@2022',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if (isset($data['token'])) {
                    session([
                        'api_token' => $data['token'],
                        'api_token_obtained_at' => now(),
                    ]);
                    Log::info('API login successful, token stored in session.');
                    return $data['token'];
                }
            }

            Log::error('API login failed.', ['response' => $response->getBody()->getContents()]);
        } catch (\Exception $e) {
            Log::error('Error during API login:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

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

        // Simpan filter ke dalam session untuk caching pilihan filter
        session([
            'id_asrama' => $request->id_asrama,
            'tingkat_pelanggaran' => $request->tingkat_pelanggaran,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // Cek apakah ada salah satu parameter filter diisi
        if ($request->hasAny(['id_asrama', 'tingkat_pelanggaran', 'start_date', 'end_date', 'day', 'month', 'year'])) {
            // Validasi
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
                return view('app.pelanggaran', compact('data', 'errors', 'tingkatPelanggaranLabels'));
            }

            try {
                $formParams = array_filter([
                    'id_asrama' => $request->id_asrama,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'day' => $request->day,
                    'month' => $request->month,
                    'year' => $request->year,
                ]);

                if (empty($request->tingkat_pelanggaran)) {
                    // Jika user memilih "Semua Tingkat"
                    $levels = [1, 2, 3, 4, 5, 6, 7];
                    $dataPerLevel = [];
                    $namaAsrama = null;
                    $totalKeseluruhan = 0;

                    foreach ($levels as $level) {
                        // Cache key per level
                        $paramsWithLevel = array_merge($formParams, ['tingkat_pelanggaran' => $level]);
                        $cacheKey = 'pelanggaranByAsrama_' . md5(json_encode($paramsWithLevel));

                        $levelData = Cache::remember($cacheKey, 300, function () use ($apiToken, $paramsWithLevel) {
                            $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);
                            $response = $client->post('https://cis-dev.del.ac.id/api/statistik-api/get-total-pelanggaran-by-asrama', [
                                'headers' => [
                                    'Authorization' => "Bearer $apiToken",
                                    'Accept' => 'application/json',
                                ],
                                'query' => $paramsWithLevel,
                            ]);

                            if ($response->getStatusCode() === 200) {
                                return json_decode($response->getBody(), true);
                            }

                            Log::warning('Unexpected response from API.', ['status' => $response->getStatusCode()]);
                            return null;
                        });

                        if (isset($levelData['result']) && $levelData['result'] == 'OK' && isset($levelData['data'])) {
                            $totalPelanggaran = $levelData['data']['total_pelanggaran'] ?? 0;
                            $dataPerLevel[$level] = $totalPelanggaran;
                            $namaAsrama = $levelData['data']['nama_asrama'] ?? $namaAsrama;
                            $totalKeseluruhan += $totalPelanggaran;
                        } else {
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
                    // Jika hanya satu tingkat dipilih
                    $paramsWithLevel = array_merge($formParams, [
                        'tingkat_pelanggaran' => $request->tingkat_pelanggaran,
                    ]);

                    $cacheKey = 'pelanggaranByAsrama_' . md5(json_encode($paramsWithLevel));

                    $data = Cache::remember($cacheKey, 300, function () use ($apiToken, $paramsWithLevel) {
                        $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);
                        $response = $client->post('https://cis-dev.del.ac.id/api/statistik-api/get-total-pelanggaran-by-asrama', [
                            'headers' => [
                                'Authorization' => "Bearer $apiToken",
                                'Accept' => 'application/json',
                            ],
                            'query' => $paramsWithLevel,
                        ]);

                        if ($response->getStatusCode() === 200) {
                            return json_decode($response->getBody(), true);
                        }

                        Log::warning('Unexpected response from API.', ['status' => $response->getStatusCode()]);
                        return null;
                    });
                }

                // Cek apakah data sesuai format
                if (!isset($data['result']) || $data['result'] !== 'OK' || !isset($data['data'])) {
                    // Data tidak sesuai, berikan fallback minimal
                    $data = [
                        'result' => 'ERROR',
                        'data' => [
                            'nama_asrama' => '-',
                            'total_keseluruhan' => 0,
                            'pelanggaran_per_level' => []
                        ]
                    ];
                }

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

        return view('app.pelanggaran', compact('data', 'errors', 'tingkatPelanggaranLabels'));
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

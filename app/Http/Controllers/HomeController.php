<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;

class HomeController extends Controller
{
    /**
     * Display the home page with mahasiswa data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Retrieve filters from request or session to maintain the last filter
        $angkatan = $request->has('angkatan') ? $request->get('angkatan', '') : session('last_angkatan', '');
        $prodi = $request->has('prodi') ? $request->get('prodi', '') : session('last_prodi', '');

        // Save the last selections to the session
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
            $errors[] = 'Unable to authenticate with the API. Please try again later.';
            return view('app.home', compact('dataMahasiswa', 'prodiList', 'angkatan', 'prodi', 'errors'));
        }

        try {
            $client = new Client([
                'verify' => false,
                'timeout' => 60,
                'http_errors' => false, // Disable throwing exceptions on HTTP errors
            ]);

            // Create a unique cache key based on filters
            $cacheKey = 'dataMahasiswa_' . md5(json_encode(['angkatan' => $angkatan, 'prodi' => $prodi]));

            if (Cache::has($cacheKey)) {
                // Retrieve data from cache
                $dataMahasiswa = Cache::get($cacheKey);
            } else {
                // Data not cached, perform requests based on conditions
                if (empty($angkatan) && empty($prodi)) {
                    // All Prodi, All Angkatan
                    $promises = [];
                    foreach ($prodiList as $prodiId => $prodiName) {
                        $queryParams = [
                            'angkatan' => '',
                            'prodi' => $prodiId,
                        ];
                        $promises[$prodiName] = $this->makeApiRequest($client, $apiToken, 'https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', $queryParams);
                    }

                    $responses = Utils::settle($promises)->wait();
                    $grandTotal = 0;
                    foreach ($responses as $prodiName => $result) {
                        if ($result['state'] === 'fulfilled') {
                            $response = $result['value'];
                            if ($response->getStatusCode() === 200) {
                                $data = json_decode($response->getBody()->getContents(), true);
                                $value = $data['total'] ?? 0;
                                $dataMahasiswa[$prodiName] = (int) $value;
                                $grandTotal += (int) $value;
                            } else {
                                $errors[] = "Failed to fetch data for program: {$prodiName}.";
                            }
                        } else {
                            $errors[] = "Failed to fetch data for program: {$prodiName}. Reason: " . $result['reason']->getMessage();
                        }
                    }
                    $dataMahasiswa['total'] = $grandTotal;

                } elseif (empty($angkatan) && !empty($prodi)) {
                    // All angkatan but prodi is set
                    $currentYear = date('Y');
                    $angkatanRange = range($currentYear - 6, $currentYear);
                    $promises = [];
                    foreach ($angkatanRange as $year) {
                        $queryParams = [
                            'angkatan' => $year,
                            'prodi' => $prodi,
                        ];
                        $promises[$year] = $this->makeApiRequest($client, $apiToken, 'https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', $queryParams);
                    }

                    $responses = Utils::settle($promises)->wait();
                    foreach ($responses as $year => $result) {
                        if ($result['state'] === 'fulfilled') {
                            $response = $result['value'];
                            if ($response->getStatusCode() === 200) {
                                $data = json_decode($response->getBody()->getContents(), true);
                                $dataMahasiswa[$year] = $data['total'] ?? 0;
                            } else {
                                $errors[] = "Failed to fetch data for year: {$year}.";
                            }
                        } else {
                            $errors[] = "Failed to fetch data for year: {$year}. Reason: " . $result['reason']->getMessage();
                        }
                    }

                } elseif (!empty($angkatan) && empty($prodi)) {
                    // All prodi but angkatan is set
                    $promises = [];
                    foreach ($prodiList as $prodiId => $prodiName) {
                        $queryParams = [
                            'angkatan' => $angkatan,
                            'prodi' => $prodiId,
                        ];
                        $promises[$prodiName] = $this->makeApiRequest($client, $apiToken, 'https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', $queryParams);
                    }

                    $responses = Utils::settle($promises)->wait();
                    foreach ($responses as $prodiName => $result) {
                        if ($result['state'] === 'fulfilled') {
                            $response = $result['value'];
                            if ($response->getStatusCode() === 200) {
                                $data = json_decode($response->getBody()->getContents(), true);
                                $dataMahasiswa[$prodiName] = $data['total'] ?? 0;
                            } else {
                                $errors[] = "Failed to fetch data for program: {$prodiName}.";
                            }
                        } else {
                            $errors[] = "Failed to fetch data for program: {$prodiName}. Reason: " . $result['reason']->getMessage();
                        }
                    }

                } else {
                    // Both parameters are set (single request)
                    $queryParams = [
                        'angkatan' => $angkatan,
                        'prodi' => $prodi,
                    ];
                    $response = $this->makeApiRequest($client, $apiToken, 'https://cis-dev.del.ac.id/api/library-api/get-total-mahasiswa-aktif', $queryParams)->wait();

                    if ($response->getStatusCode() === 200) {
                        $data = json_decode($response->getBody()->getContents(), true);
                        if (isset($data['total'])) {
                            $dataMahasiswa = ['total' => (int) $data['total']];
                        } else {
                            $errors[] = 'Data tidak ditemukan.';
                        }
                    } else {
                        $errors[] = 'Failed to fetch data: ' . $response->getReasonPhrase();
                    }
                }

                // Save the result to cache for 5 minutes (300 seconds) if no errors
                if (empty($errors)) {
                    Cache::put($cacheKey, $dataMahasiswa, 300);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error fetching data from API:', ['message' => $e->getMessage()]);
            $errors[] = 'Terjadi kesalahan saat menghubungi API.';
        }

        return view('app.home', compact('dataMahasiswa', 'prodiList', 'angkatan', 'prodi', 'errors'));
    }

    /**
     * Make an API request with automatic token refresh on 401 Unauthorized.
     *
     * @param  \GuzzleHttp\Client  $client
     * @param  string  $apiToken
     * @param  string  $url
     * @param  array  $queryParams
     * @param  bool  $retry
     * @return \GuzzleHttp\Promise\PromiseInterface|\GuzzleHttp\Psr7\Response
     */
    protected function makeApiRequest(Client $client, $apiToken, $url, array $queryParams, $retry = true)
    {
        return $client->getAsync($url, [
            'headers' => [
                'Authorization' => "Bearer {$apiToken}",
                'Accept' => 'application/json',
            ],
            'query' => $queryParams,
        ])->then(
                function ($response) use ($client, $apiToken, $url, $queryParams, $retry) {
                    if ($response->getStatusCode() === 200) {
                        // Successful response
                        return $response;
                    } elseif ($response->getStatusCode() === 401 && $retry) {
                        // Unauthorized, attempt to refresh token
                        Log::warning('API token expired. Attempting to refresh token.');

                        $newApiToken = $this->getApiToken();
                        if ($newApiToken) {
                            // Retry the request with the new token
                            return $client->getAsync($url, [
                                'headers' => [
                                    'Authorization' => "Bearer {$newApiToken}",
                                    'Accept' => 'application/json',
                                ],
                                'query' => $queryParams,
                            ])->wait();
                        } else {
                            // Unable to refresh token
                            Log::error('Failed to refresh API token.');
                            return $response;
                        }
                    }

                    // For other status codes, return the response as is
                    return $response;
                },
                function ($exception) {
                    // Handle network or other errors
                    Log::error('Network or other error during API request:', ['message' => $exception->getMessage()]);
                    throw $exception; // Let the exception propagate
                }
            );
    }

    /**
     * Obtain a new API token and store it in the session.
     *
     * @return string|null
     */
    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $client = new Client([
                'verify' => false,
                'timeout' => 60,
                'stream' => true,
                'http_errors' => false, // Disable throwing exceptions on HTTP errors
            ]);

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
                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['token'])) {
                    session(['api_token' => $data['token']]);
                    Log::info('New API token retrieved:', ['token' => $data['token']]);
                    return $data['token'];
                }

                Log::error('Failed to retrieve API token.', ['response' => $data]);
            } else {
                Log::error('API login failed with status:', ['status' => $response->getStatusCode(), 'body' => $response->getBody()->getContents()]);
            }
        } catch (RequestException $e) {
            Log::error('Error during API token retrieval:', ['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Unexpected error during API token retrieval:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

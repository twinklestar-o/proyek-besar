<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use App\Models\Section;

class LogController extends Controller
{
    public function getLogMahasiswa(Request $request)
    {
        $dataMasuk = null;
        $dataKeluar = null;
        $errors = [];

        // Retrieve sections
        $sections = Section::all()->keyBy('section');

        // Retrieve filters from request or session
        $startMasuk = $request->input('start_masuk', session('last_start_masuk', ''));
        $endMasuk = $request->input('end_masuk', session('last_end_masuk', ''));
        $startKeluar = $request->input('start_keluar', session('last_start_keluar', ''));
        $endKeluar = $request->input('end_keluar', session('last_end_keluar', ''));

        // Store current filters in session
        session([
            'last_start_masuk' => $startMasuk,
            'last_end_masuk' => $endMasuk,
            'last_start_keluar' => $startKeluar,
            'last_end_keluar' => $endKeluar,
        ]);

        $hasMasukParams = !empty($startMasuk) || !empty($endMasuk);
        $hasKeluarParams = !empty($startKeluar) || !empty($endKeluar);

        // Validate request inputs
        $request->validate([
            'start_masuk' => 'nullable|date',
            'end_masuk' => 'nullable|date',
            'start_keluar' => 'nullable|date',
            'end_keluar' => 'nullable|date',
            'day' => 'nullable|string',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2099',
        ]);

        if (!$hasMasukParams && !$hasKeluarParams) {
            $errors = ['Harap isi setidaknya salah satu parameter.'];
            return view('app.log', compact('sections', 'dataMasuk', 'dataKeluar', 'errors', 'startMasuk', 'endMasuk', 'startKeluar', 'endKeluar'));
        }

        // Obtain API token
        $apiToken = session('api_token');
        if (!$apiToken) {
            $apiToken = $this->getApiToken();
            if (!$apiToken) {
                $errors[] = 'Unable to authenticate with the API. Please try again later.';
                return view('app.log', compact('sections', 'dataMasuk', 'dataKeluar', 'errors', 'startMasuk', 'endMasuk', 'startKeluar', 'endKeluar'));
            }
        }

        try {
            $client = new Client([
                'verify' => false,
                'timeout' => 10,
                'http_errors' => false,
            ]);

            $promises = [];

            if ($hasMasukParams) {
                $formParamsMasuk = array_filter([
                    'start_masuk' => $startMasuk,
                    'end_masuk' => $endMasuk,
                    'day' => $request->input('day'),
                    'month' => $request->input('month'),
                    'year' => $request->input('year'),
                ]);
                $promises['masuk'] = $this->getLogPromise($client, $apiToken, $formParamsMasuk);
            }

            if ($hasKeluarParams) {
                $formParamsKeluar = array_filter([
                    'start_keluar' => $startKeluar,
                    'end_keluar' => $endKeluar,
                    'day' => $request->input('day'),
                    'month' => $request->input('month'),
                    'year' => $request->input('year'),
                ]);
                $promises['keluar'] = $this->getLogPromise($client, $apiToken, $formParamsKeluar);
            }

            // Tunggu semua promise selesai
            $results = Utils::settle($promises)->wait();

            // Process Masuk Logs
            if (isset($results['masuk'])) {
                if ($results['masuk']['state'] === 'fulfilled') {
                    $response = $results['masuk']['value'];
                    if ($response->getStatusCode() === 200) {
                        $body = $response->getBody()->getContents();
                        $dataMasuk = json_decode($body, true);
                    } else {
                        $errors[] = 'Failed to fetch Log Masuk: ' . $response->getReasonPhrase();
                    }
                } else {
                    $errors[] = 'Failed to fetch Log Masuk: ' . $results['masuk']['reason']->getMessage();
                }
            }

            // Process Keluar Logs
            if (isset($results['keluar'])) {
                if ($results['keluar']['state'] === 'fulfilled') {
                    $response = $results['keluar']['value'];
                    if ($response->getStatusCode() === 200) {
                        $body = $response->getBody()->getContents();
                        $dataKeluar = json_decode($body, true);
                    } else {
                        $errors[] = 'Failed to fetch Log Keluar: ' . $response->getReasonPhrase();
                    }
                } else {
                    $errors[] = 'Failed to fetch Log Keluar: ' . $results['keluar']['reason']->getMessage();
                }
            }

        } catch (\Exception $e) {
            Log::error('Error fetching Log Mahasiswa data:', ['message' => $e->getMessage()]);
            $errors[] = 'Unable to fetch data from the API. Please try again later.';
        }

        return view('app.log', compact('sections', 'dataMasuk', 'dataKeluar', 'errors', 'startMasuk', 'endMasuk', 'startKeluar', 'endKeluar'));
    }

    protected function getLogPromise(Client $client, $apiToken, array $formParams, $retry = true)
    {
        $cacheKey = 'logMahasiswa_' . md5(json_encode($formParams));

        // Jika data ada di cache, langsung return promise yang telah terpenuhi
        if (Cache::has($cacheKey)) {
            return \GuzzleHttp\Promise\Create::promiseFor(
                new \GuzzleHttp\Psr7\Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode(Cache::get($cacheKey))
                )
            );
        }

        return $client->postAsync('https://cis-dev.del.ac.id/api/statistik-api/get-total-log-mhs', [
            'headers' => [
                'Authorization' => "Bearer $apiToken",
                'Accept' => 'application/json',
            ],
            'query' => $formParams,
        ])->then(
                function ($response) use ($cacheKey, $formParams, $client, &$apiToken, $retry) {
                    if ($response->getStatusCode() === 200) {
                        $body = $response->getBody()->getContents();
                        $data = json_decode($body, true);

                        // Cache jika result = OK
                        if (isset($data['result']) && $data['result'] === 'OK') {
                            Cache::put($cacheKey, $data, 300); // cache 5 menit
                        }

                        return new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], $body);
                    } elseif ($response->getStatusCode() === 401 && $retry) {
                        // Token kadaluarsa, coba refresh
                        Log::warning('API token expired. Attempting to refresh token.');
                        $newApiToken = $this->getApiToken();
                        if ($newApiToken) {
                            return $client->postAsync('https://cis-dev.del.ac.id/api/statistik-api/get-total-log-mhs', [
                                'headers' => [
                                    'Authorization' => "Bearer $newApiToken",
                                    'Accept' => 'application/json',
                                ],
                                'query' => $formParams,
                            ])->then(function ($newResponse) use ($cacheKey) {
                                if ($newResponse->getStatusCode() === 200) {
                                    $body = $newResponse->getBody()->getContents();
                                    $data = json_decode($body, true);

                                    if (isset($data['result']) && $data['result'] === 'OK') {
                                        Cache::put($cacheKey, $data, 300);
                                    }

                                    return new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], $body);
                                }
                                return $newResponse;
                            });
                        } else {
                            Log::error('Failed to refresh API token.');
                            return $response;
                        }
                    }

                    return $response;
                },
                function ($exception) {
                    Log::error('Network or other error during API request:', ['message' => $exception->getMessage()]);
                    throw $exception;
                }
            );
    }

    protected function getApiToken()
    {
        try {
            Log::info('Attempting API login...');
            $client = new Client([
                'verify' => false,
                'timeout' => 10,
                'http_errors' => false,
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
        } catch (RequestException $e) {
            Log::error('Error during API login:', ['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Unexpected error during API login:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

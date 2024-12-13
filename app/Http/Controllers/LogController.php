<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class LogController extends Controller
{
    public function getLogMahasiswa(Request $request)
    {
        $dataMasuk = null;
        $dataKeluar = null;
        $errors = [];

        // Cek apakah ada parameter masuk atau keluar
        $hasMasukParams = $request->filled('start_masuk') || $request->filled('end_masuk');
        $hasKeluarParams = $request->filled('start_keluar') || $request->filled('end_keluar');

        // Validasi parameter
        $request->validate([
            'start_masuk' => 'nullable|date',
            'end_masuk' => 'nullable|date',
            'start_keluar' => 'nullable|date',
            'end_keluar' => 'nullable|date',
            'day' => 'nullable|string',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2099',
        ]);

        // Hanya lanjutkan jika ada parameter (masuk/keluar) yang terisi
        if ($hasMasukParams || $hasKeluarParams) {

            $apiToken = session('api_token') ?? $this->getApiToken();

            if (!$apiToken) {
                Log::error('Failed to obtain API token.');
                $errors = ['Unable to authenticate with the API. Please try again later.'];
                return view('app.log', compact('dataMasuk', 'dataKeluar', 'errors'));
            }

            try {
                // Jika ada parameter masuk
                if ($hasMasukParams) {
                    $formParamsMasuk = array_filter([
                        'start_masuk' => $request->start_masuk,
                        'end_masuk' => $request->end_masuk,
                        // Param lain seperti day, month, year jika perlu.
                        'day' => $request->day,
                        'month' => $request->month,
                        'year' => $request->year,
                    ]);
                    $dataMasuk = $this->fetchLogData($apiToken, $formParamsMasuk, 'masuk');
                }

                // Jika ada parameter keluar
                if ($hasKeluarParams) {
                    $formParamsKeluar = array_filter([
                        'start_keluar' => $request->start_keluar,
                        'end_keluar' => $request->end_keluar,
                        // Param lain seperti day, month, year jika perlu.
                        'day' => $request->day,
                        'month' => $request->month,
                        'year' => $request->year,
                    ]);
                    $dataKeluar = $this->fetchLogData($apiToken, $formParamsKeluar, 'keluar');
                }

                if (!$dataMasuk && !$dataKeluar) {
                    $errors = ['Unable to fetch data from the API. Please try again later.'];
                }

            } catch (\Exception $e) {
                if ($e->getCode() === 401) {
                    Log::warning('Token expired, attempting to refresh...');
                    session()->forget('api_token');
                    $apiToken = $this->getApiToken();

                    if ($apiToken) {
                        return $this->getLogMahasiswa($request);
                    }
                }

                Log::error('Error fetching Log Mahasiswa data:', ['message' => $e->getMessage()]);
                $errors = ['Unable to fetch data from the API. Please try again later.'];
            }
        } else {
            // Jika tidak ada parameter sama sekali
            $errors = ['Harap isi setidaknya salah satu parameter.'];
        }

        return view('app.log', compact('dataMasuk', 'dataKeluar', 'errors'));
    }

    protected function fetchLogData($apiToken, $formParams, $tipe)
    {
        $cacheKey = 'logMahasiswa_' . $tipe . '_' . md5(json_encode($formParams));

        return Cache::remember($cacheKey, 300, function () use ($apiToken, $formParams) {
            $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);
            $response = $client->post('https://cis-dev.del.ac.id/api/statistik-api/get-total-log-mhs', [
                'headers' => [
                    'Authorization' => "Bearer $apiToken",
                    'Accept' => 'application/json',
                ],
                'query' => $formParams,
            ]);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                return $data;
            }

            return null;
        });
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

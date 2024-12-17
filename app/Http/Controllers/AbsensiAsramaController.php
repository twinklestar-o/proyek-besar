<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use App\Models\Section;

class AbsensiAsramaController extends Controller
{
    /**
     * Menampilkan data Absensi Asrama dengan filter yang diberikan.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getAbsensiAsrama(Request $request)
    {
        $data = null;
        $errors = [];

        // Retrieve sections for Absensi Asrama
        $sections = Section::all()->keyBy('section');

        // Retrieve filters from request or session
        $idAsrama = $request->input('id_asrama', session('absensi_asrama.last_id_asrama', ''));
        $startTime = $request->input('start_time', session('absensi_asrama.last_start_time', ''));
        $endTime = $request->input('end_time', session('absensi_asrama.last_end_time', ''));
        $day = $request->input('day', session('absensi_asrama.last_day', ''));
        $month = $request->input('month', session('absensi_asrama.last_month', ''));
        $year = $request->input('year', session('absensi_asrama.last_year', ''));

        // Store current filters in session
        session([
            'absensi_asrama.last_id_asrama' => $idAsrama,
            'absensi_asrama.last_start_time' => $startTime,
            'absensi_asrama.last_end_time' => $endTime,
            'absensi_asrama.last_day' => $day,
            'absensi_asrama.last_month' => $month,
            'absensi_asrama.last_year' => $year,
        ]);

        // Validasi input
        $request->validate([
            'id_asrama' => 'nullable|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'day' => 'nullable|integer|min:1|max:31',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2099',
        ]);

        // Pastikan 'id_asrama' diisi
        if (empty($idAsrama)) {
            return view('app.absensi_asrama', compact('data', 'sections', 'errors', 'idAsrama', 'startTime', 'endTime', 'day', 'month', 'year'));
        }

        try {
            $data = $this->fetchAbsensiAsramaData($idAsrama, $startTime, $endTime, $day, $month, $year);
            if (!$data) {
                $errors[] = 'Data tidak tersedia atau terjadi kesalahan saat mengambil data.';
            }
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data Absensi Asrama:', ['message' => $e->getMessage()]);
            $errors[] = 'Terjadi kesalahan saat mengambil data Absensi Asrama.';
        }

        return view('app.absensi_asrama', compact('data', 'sections', 'errors', 'idAsrama', 'startTime', 'endTime', 'day', 'month', 'year'));
    }

    /**
     * Fetches the Absensi Asrama data from the API with token handling.
     *
     * @param string $idAsrama
     * @param string|null $startTime
     * @param string|null $endTime
     * @param int|null $day
     * @param int|null $month
     * @param int|null $year
     * @return array|null
     */
    protected function fetchAbsensiAsramaData($idAsrama, $startTime, $endTime, $day, $month, $year)
    {
        $queryParams = array_filter([
            'id_asrama' => $idAsrama,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'day' => $day,
            'month' => $month,
            'year' => $year,
        ]);

        $cacheKey = 'absensiAsrama_' . md5(json_encode($queryParams));
        $apiToken = session('api_token') ?? $this->getApiToken();

        if (!$apiToken) {
            throw new \Exception('Tidak dapat mengautentikasi dengan API.');
        }

        try {
            return Cache::remember($cacheKey, 300, function () use ($apiToken, $queryParams) {
                return $this->makeApiRequest($apiToken, $queryParams);
            });
        } catch (\Exception $e) {
            if ($e->getCode() === 401) {
                Log::warning('Token API kadaluarsa. Mencoba refresh token...');
                session()->forget('api_token');
                $apiToken = $this->getApiToken();

                if ($apiToken) {
                    return $this->makeApiRequest($apiToken, $queryParams);
                }

                throw new \Exception('Gagal mendapatkan token API baru.');
            }

            throw $e;
        }
    }

    /**
     * Makes an API request to fetch Absensi Asrama data.
     *
     * @param string $apiToken
     * @param array $queryParams
     * @return array|null
     */
    protected function makeApiRequest($apiToken, $queryParams)
    {
        $client = new Client(['verify' => false, 'timeout' => 60, 'http_errors' => false]);
        $response = $client->get('https://cis-dev.del.ac.id/api/statistik-api/get-total-absensi-by-asrama', [
            'headers' => [
                'Authorization' => "Bearer $apiToken",
                'Accept' => 'application/json',
            ],
            'query' => $queryParams,
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), true);
        }

        Log::error('Respons API tidak terduga.', ['status' => $response->getStatusCode()]);
        throw new \Exception('Error saat melakukan request ke API.', $response->getStatusCode());
    }

    /**
     * Mendapatkan token API dengan mencoba autentikasi ulang.
     *
     * @return string|null
     */
    protected function getApiToken()
    {
        try {
            Log::info('Mencoba login ke API...');
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
                $data = json_decode($response->getBody(), true);
                $apiToken = $data['token'] ?? null;

                if ($apiToken) {
                    session(['api_token' => $apiToken]);
                    Log::info('Login API berhasil. Token disimpan di session.', ['token' => $apiToken]);
                    return $apiToken;
                }
            }

            Log::error('Login API gagal.', ['response' => $response->getBody()->getContents()]);
        } catch (\Exception $e) {
            Log::error('Error saat login ke API:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

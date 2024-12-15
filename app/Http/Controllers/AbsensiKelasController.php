<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class AbsensiKelasController extends Controller
{
    // Daftar Prodi
    private $prodiList = [
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

    // Menampilkan halaman awal dengan form filter
    public function showAbsensiKelas(Request $request)
    {
        $data = null;
        $tahunKurikulum = $this->getTahunKurikulum();

        // Buat pilihan tahun ajar (7 tahun terakhir)
        $currentYear = date('Y');
        $tahunAjarList = [];
        for ($y = $currentYear; $y >= $currentYear - 6; $y--) { // 7 tahun termasuk tahun sekarang
            $tahunAjarList[] = $y;
        }

        return view('app.absensi_kelas', [
            'data' => $data,
            'tahunKurikulum' => $tahunKurikulum,
            'prodiList' => $this->prodiList,
            'tahunAjarList' => $tahunAjarList,
        ]);
    }

    // Mengambil daftar Mata Kuliah via AJAX
    public function getMatkulAjax(Request $request)
    {
        if ($request->ajax()) {
            $prodi_id = $request->input('prodi_id');
            $semester = $request->input('semester');
            $ta = $request->input('ta');

            // Validasi input
            $validator = \Validator::make($request->all(), [
                'prodi_id' => 'required|integer|in:1,3,4,6,7,8,9,10,16',
                'semester' => 'required|integer|min:1|max:8',
                'ta' => 'required|integer',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi AJAX getMatkulAjax gagal.', ['errors' => $validator->errors()]);
                return response()->json([
                    'error' => 'Input tidak valid.',
                    'details' => $validator->errors()
                ], 400);
            }

            // Tentukan sem_ta berdasarkan parity semester
            $sem_ta = ($semester % 2 !== 0) ? 1 : 2;
            Log::info("Menentukan sem_ta berdasarkan semester: $semester, sem_ta: $sem_ta");

            // Panggil API get matkul
            $matkulList = $this->getMatkulByProdiSemTa($prodi_id, $sem_ta, $ta);
            Log::info('Daftar Mata Kuliah diperoleh dari API.', ['matkulList' => $matkulList]);

            // Memfilter matkulList berdasarkan semester yang dipilih
            $filteredMatkulList = array_values(array_filter($matkulList, function ($matkul) use ($semester) {
                return isset($matkul['sem']) && $matkul['sem'] == $semester;
            }));
            Log::info('Daftar Mata Kuliah setelah difilter berdasarkan semester.', ['filteredMatkulList' => $filteredMatkulList]);

            // Dapatkan tahun kurikulum
            $tahunKurikulum = $this->getTahunKurikulum();
            Log::info('Daftar Tahun Kurikulum diperoleh dari API.', ['tahunKurikulum' => $tahunKurikulum]);

            return response()->json([
                'matkulList' => $filteredMatkulList,
                'tahunKurikulum' => $tahunKurikulum,
            ]);
        }

        return response()->json(['error' => 'Permintaan tidak valid.'], 400);
    }

    // Mengambil data Absensi via AJAX
    public function fetchTotalKehadiranAjax(Request $request)
    {
        if ($request->ajax()) {
            $kode_mk = $request->input('kode_mk');
            $id_kur = $request->input('id_kur');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');

            // Validasi input
            $validator = \Validator::make($request->all(), [
                'kode_mk' => 'required|string',
                'id_kur' => 'required|string',
                'start_time' => 'nullable|date',
                'end_time' => 'nullable|date|after_or_equal:start_time',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi AJAX fetchTotalKehadiranAjax gagal.', ['errors' => $validator->errors()]);
                return response()->json([
                    'error' => 'Input tidak valid.',
                    'details' => $validator->errors()
                ], 400);
            }

            $apiToken = session('api_token') ?? $this->getApiToken();

            if (!$apiToken) {
                Log::error('Gagal mendapatkan token API.');
                return response()->json(['error' => 'Tidak dapat mengautentikasi dengan API.'], 401);
            }

            try {
                $queryParams = array_filter([
                    'kode_mk' => $kode_mk,
                    'id_kur' => $id_kur,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                ]);

                $cacheKey = 'absensiKelas_' . md5(json_encode($queryParams));
                $data = Cache::remember($cacheKey, 300, function () use ($apiToken, $queryParams) {
                    $client = new Client(['verify' => false, 'stream' => true, 'timeout' => 10]);
                    $response = $client->get('https://cis-dev.del.ac.id/api/statistik-api/get-total-kehadiran-mhs', [
                        'headers' => ['Authorization' => "Bearer $apiToken"],
                        'query' => $queryParams,
                    ]);

                    return $response->getStatusCode() === 200
                        ? json_decode($response->getBody(), true)
                        : null;
                });

                Log::info('Respons API Absensi Kelas:', ['response' => $data]);

                if ($data && isset($data['data'])) {
                    return response()->json(['data' => $data['data']], 200);
                } else {
                    return response()->json(['error' => 'Data absensi tidak ditemukan.'], 404);
                }
            } catch (\Exception $e) {
                Log::error('Error saat mengambil data Absensi Kelas:', ['message' => $e->getMessage()]);
                return response()->json(['error' => 'Terjadi kesalahan saat mengambil data.'], 500);
            }
        }

        return response()->json(['error' => 'Permintaan tidak valid.'], 400);
    }

    // Mengambil daftar Mata Kuliah berdasarkan Prodi, sem_ta, dan ta
    private function getMatkulByProdiSemTa($prodi_id, $sem_ta, $ta)
    {
        $matkulList = [];
        $apiToken = session('api_token') ?? $this->getApiToken();

        if (!$apiToken) {
            Log::error('Gagal mendapatkan token API dalam getMatkulByProdiSemTa.');
            return [];
        }

        $client = new Client(['verify' => false, 'timeout' => 10]);

        try {
            Log::info('Memanggil API Mata Kuliah dengan prodi_id:', ['prodi_id' => $prodi_id, 'sem_ta' => $sem_ta, 'ta' => $ta]);
            $response = $client->get('https://cis-dev.del.ac.id/api/library-api/matkul-by-prodi-sem-ta', [
                'headers' => [
                    'Authorization' => "Bearer $apiToken",
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'prodi_id' => $prodi_id,
                    'sem_ta' => $sem_ta,
                    'ta' => $ta,
                ],
            ]);

            Log::info('Respons API Mata Kuliah:', ['status' => $response->getStatusCode()]);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['data']) && is_array($data['data'])) {
                    $matkulList = $data['data'];
                    Log::info('Mata Kuliah berhasil diambil dari API.', ['jumlah_matkul' => count($matkulList)]);
                } else {
                    Log::warning('Data Mata Kuliah tidak ditemukan dalam respons API.', ['data' => $data]);
                }
            } else {
                Log::warning('Respons tidak terduga dari API Mata Kuliah.', ['status' => $response->getStatusCode()]);
            }
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data Mata Kuliah:', ['message' => $e->getMessage()]);
        }

        // Menghapus duplikasi berdasarkan kode_mk
        $matkulList = collect($matkulList)->unique('kode_mk')->values()->toArray();

        return $matkulList;
    }

    // Mengambil daftar Tahun Kurikulum
    private function getTahunKurikulum()
    {
        $tahunKurikulum = [];
        try {
            $apiToken = session('api_token') ?? $this->getApiToken();

            if (!$apiToken) {
                Log::error('Gagal mendapatkan token API.');
                return [];
            }

            $client = new Client(['verify' => false, 'timeout' => 10]);
            $response = $client->get('https://cis-dev.del.ac.id/api/library-api/get-tahun-kurikulum', [
                'headers' => ['Authorization' => "Bearer $apiToken"],
            ]);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                $tahunKurikulum = $data['data'] ?? [];
            } else {
                Log::warning('Respons tidak terduga dari API Tahun Kurikulum.', ['status' => $response->getStatusCode()]);
            }
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data Tahun Kurikulum:', ['message' => $e->getMessage()]);
        }

        return $tahunKurikulum;
    }

    // Mendapatkan token API melalui login
    protected function getApiToken()
    {
        try {
            Log::info('Mencoba login API...');
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
            Log::error('Error saat login API:', ['message' => $e->getMessage()]);
        }

        return null;
    }
}

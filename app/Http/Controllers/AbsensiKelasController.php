<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\Section;
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
        // Ambil filter dari session
        $prodi_id = $request->input('prodi_id', session('absensi.prodi_id', ''));
        $semester = $request->input('semester', session('absensi.semester', ''));
        $ta = $request->input('ta', session('absensi.ta', ''));
        $kode_mk = $request->input('kode_mk', session('absensi.kode_mk', ''));
        $id_kur = $request->input('id_kur', session('absensi.id_kur', ''));
        $start_time = $request->input('start_time', session('absensi.start_time', ''));
        $end_time = $request->input('end_time', session('absensi.end_time', ''));

        // Simpan filter ke session
        session([
            'absensi.prodi_id' => $prodi_id,
            'absensi.semester' => $semester,
            'absensi.ta' => $ta,
            'absensi.kode_mk' => $kode_mk,
            'absensi.id_kur' => $id_kur,
            'absensi.start_time' => $start_time,
            'absensi.end_time' => $end_time,
        ]);

        // Buat pilihan tahun ajar (7 tahun terakhir)
        $currentYear = date('Y');
        $tahunAjarList = [];
        for ($y = $currentYear; $y >= $currentYear - 6; $y--) { // 7 tahun termasuk tahun sekarang
            $tahunAjarList[] = $y;
        }

        // Dapatkan tahun kurikulum (dengan caching)
        $tahunKurikulum = $this->getTahunKurikulum();

        // Inisialisasi variabel untuk Mata Kuliah dan Absensi
        $matkulList = [];
        $absensiData = [];

        // Retrieve sections for Absensi Asrama
        $sections = Section::all()->keyBy('section');

        // Jika prodi_id, semester, dan ta sudah dipilih, ambil Mata Kuliah dari cache atau API
        if ($prodi_id && $semester && $ta) {
            $matkulList = $this->getMatkulList($prodi_id, $semester, $ta);
        }

        // Jika kode_mk dan id_kur sudah dipilih, ambil Data Absensi dari cache atau API
        if ($kode_mk && $id_kur) {
            $absensiData = $this->getAbsensiData($kode_mk, $id_kur, $start_time, $end_time);
        }

        return view('app.absensi_kelas', [
            'prodiList' => $this->prodiList,
            'tahunAjarList' => $tahunAjarList,
            'tahunKurikulum' => $tahunKurikulum,
            'selectedProdi' => $prodi_id,
            'selectedSemester' => $semester,
            'selectedTa' => $ta,
            'selectedMatkul' => $kode_mk,
            'selectedKurikulum' => $id_kur,
            'selectedStartTime' => $start_time,
            'selectedEndTime' => $end_time,
            'matkulList' => $matkulList,
            'absensiData' => $absensiData,
            'sections' => $sections,
        ]);
    }

    // Mengambil daftar Mata Kuliah
    private function getMatkulList($prodi_id, $semester, $ta)
    {
        $sem_ta = ($semester % 2 !== 0) ? 1 : 2;
        $cacheKey = "matkulList_prodi_{$prodi_id}_semta_{$sem_ta}_ta_{$ta}";

        $matkulList = Cache::remember($cacheKey, 600, function () use ($prodi_id, $sem_ta, $ta) {
            $data = $this->getMatkulByProdiSemTa($prodi_id, $sem_ta, $ta);
            return empty($data) ? null : $data; // Jangan cache jika data kosong
        });

        // Muat ulang API jika cache kosong
        if (!$matkulList) {
            $matkulList = $this->getMatkulByProdiSemTa($prodi_id, $sem_ta, $ta);
            Cache::put($cacheKey, $matkulList, 600); // Cache ulang jika data valid
        }

        return array_values(array_filter($matkulList, function ($matkul) use ($semester) {
            return isset($matkul['sem']) && $matkul['sem'] == $semester;
        }));
    }

    // Mengambil data Absensi
    private function getAbsensiData($kode_mk, $id_kur, $start_time, $end_time)
    {
        $queryParams = array_filter([
            'kode_mk' => $kode_mk,
            'id_kur' => $id_kur,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);

        $cacheKey = 'absensiKelas_' . md5(json_encode($queryParams));
        $data = Cache::remember($cacheKey, 300, function () use ($queryParams) {
            $apiData = $this->fetchAbsensiDataFromAPI($queryParams);
            return empty($apiData) ? null : $apiData; // Jangan cache jika data kosong
        });

        if (!$data) {
            $data = $this->fetchAbsensiDataFromAPI($queryParams); // Muat ulang jika cache kosong
            if (!empty($data)) {
                Cache::put($cacheKey, $data, 300);
            }
        }

        return $data['data'] ?? null;
    }

    private function fetchAbsensiDataFromAPI($queryParams)
    {
        try {
            $apiToken = session('api_token') ?? $this->getApiToken();
            if (!$apiToken) {
                throw new \Exception('Token API tidak ditemukan.');
            }

            $client = new Client(['verify' => false, 'timeout' => 10, 'http_errors' => false]);
            $response = $client->get('https://cis-dev.del.ac.id/api/statistik-api/get-total-kehadiran-mhs', [
                'headers' => ['Authorization' => "Bearer $apiToken"],
                'query' => $queryParams,
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true);
            }

            Log::warning('Respons tidak terduga dari API.', ['status' => $response->getStatusCode()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error saat memuat data Absensi:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // Mengambil daftar Mata Kuliah via AJAX dengan Caching dan menyimpan seleksi ke session
    public function getMatkulAjax(Request $request)
    {
        if ($request->ajax()) {
            $prodi_id = $request->input('prodi_id');
            $semester = $request->input('semester');
            $ta = $request->input('ta');

            // Validasi input
            $validator = Validator::make($request->all(), [
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

            // Simpan filter ke session
            session([
                'absensi.prodi_id' => $prodi_id,
                'absensi.semester' => $semester,
                'absensi.ta' => $ta,
                // Reset kode_mk dan id_kur karena prodi, semester, atau ta berubah
                'absensi.kode_mk' => '',
                'absensi.id_kur' => '',
                'absensi.start_time' => '',
                'absensi.end_time' => '',
            ]);

            // Tentukan sem_ta berdasarkan parity semester
            $sem_ta = ($semester % 2 !== 0) ? 1 : 2;
            Log::info("Menentukan sem_ta berdasarkan semester: $semester, sem_ta: $sem_ta");

            // Caching key berdasarkan prodi_id, sem_ta, ta
            $cacheKey = "matkulList_prodi_{$prodi_id}_semta_{$sem_ta}_ta_{$ta}";

            // Panggil API get matkul dengan caching selama 10 menit
            $matkulList = Cache::remember($cacheKey, 600, function () use ($prodi_id, $sem_ta, $ta) {
                return $this->getMatkulByProdiSemTa($prodi_id, $sem_ta, $ta);
            });

            // Memfilter matkulList berdasarkan semester yang dipilih
            $filteredMatkulList = array_values(array_filter($matkulList, function ($matkul) use ($semester) {
                return isset($matkul['sem']) && $matkul['sem'] == $semester;
            }));
            Log::info('Daftar Mata Kuliah setelah difilter berdasarkan semester.', ['filteredMatkulList' => $filteredMatkulList]);

            // Dapatkan tahun kurikulum dengan caching
            $tahunKurikulum = Cache::remember('tahunKurikulum', 600, function () {
                return $this->getTahunKurikulum();
            });
            Log::info('Daftar Tahun Kurikulum diperoleh dari API.', ['tahunKurikulum' => $tahunKurikulum]);

            return response()->json([
                'matkulList' => $filteredMatkulList,
                'tahunKurikulum' => $tahunKurikulum,
            ]);
        }

        return response()->json(['error' => 'Permintaan tidak valid.'], 400);
    }

    // Mengambil data Absensi via AJAX dengan Caching dan menyimpan seleksi ke session
    public function fetchTotalKehadiranAjax(Request $request)
    {
        if ($request->ajax()) {
            $kode_mk = $request->input('kode_mk');
            $id_kur = $request->input('id_kur');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');

            // Validasi input
            $validator = Validator::make($request->all(), [
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

            // Simpan filter ke session
            session([
                'absensi.kode_mk' => $kode_mk,
                'absensi.id_kur' => $id_kur,
                'absensi.start_time' => $start_time,
                'absensi.end_time' => $end_time,
            ]);

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

    // Mengambil daftar Tahun Kurikulum dengan Caching
    private function getTahunKurikulum()
    {
        $tahunKurikulum = [];
        try {
            $apiToken = session('api_token') ?? $this->getApiToken();

            if (!$apiToken) {
                Log::error('Gagal mendapatkan token API.');
                return [];
            }

            $cacheKey = 'tahunKurikulum';
            $tahunKurikulum = Cache::remember($cacheKey, 600, function () use ($apiToken) {
                $client = new Client(['verify' => false, 'timeout' => 10]);
                $response = $client->get('https://cis-dev.del.ac.id/api/library-api/get-tahun-kurikulum', [
                    'headers' => ['Authorization' => "Bearer $apiToken"],
                ]);

                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody(), true);
                    return $data['data'] ?? [];
                } else {
                    Log::warning('Respons tidak terduga dari API Tahun Kurikulum.', ['status' => $response->getStatusCode()]);
                    return [];
                }
            });
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data Tahun Kurikulum:', ['message' => $e->getMessage()]);
        }

        return $tahunKurikulum;
    }

    // Mendapatkan token API melalui login dengan Caching Token
    protected function getApiToken()
    {
        try {
            Log::info('Attempting to retrieve API token...');

            // Check if there's a valid token in the session
            $apiToken = session('api_token');
            $tokenObtainedAt = session('api_token_obtained_at');

            if ($apiToken && $tokenObtainedAt) {
                $tokenAge = now()->diffInMinutes($tokenObtainedAt);
                if ($tokenAge < 60) { // Token is valid for 60 minutes
                    return $apiToken;
                }
            }

            // Token is not available or expired, try to refresh it
            Log::info('No valid token found or token expired, attempting to log in...');
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
                $data = json_decode($response->getBody(), true);

                if (isset($data['token'])) {
                    // Store the new token and timestamp in the session
                    session([
                        'api_token' => $data['token'],
                        'api_token_obtained_at' => now(),
                    ]);

                    Log::info('API login successful. Token stored in session.');
                    return $data['token'];
                }
            }

            // Log an error if the token could not be refreshed
            Log::error('Failed to log in to API.', ['response' => $response->getBody()->getContents()]);
        } catch (\Exception $e) {
            Log::error('Unexpected error during API login:', ['message' => $e->getMessage()]);
        }

        // Return null if unable to obtain a new token
        return null;
    }


    public function updateSections(Request $request)
    {
        $data = $request->all();

        // Validasi input
        $rules = [];
        foreach ($data as $key => $value) {
            if (isset($value['title'])) {
                $rules["$key.title"] = 'nullable|string|max:255';
            }
            if (isset($value['description'])) {
                $rules["$key.description"] = 'nullable|string';
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update setiap section yang ada
        foreach ($data as $sectionKey => $sectionData) {
            $section = Section::where('section', $sectionKey)->first();
            if ($section) {
                $section->update([
                    'title' => $sectionData['title'] ?? $section->title,
                    'description' => $sectionData['description'] ?? $section->description,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.',
        ]);
    }
}

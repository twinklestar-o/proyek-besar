<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;
use HTMLPurifier;
use HTMLPurifier_Config;

class ContentController extends Controller
{
    /**
     * Menyimpan perubahan sections.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateSections(Request $request)
    {
        $data = $request->all();


        $rules = [];
        foreach ($data as $key => $value) {
            if (isset($value['title'])) {
                $rules["$key.title"] = 'required|string|max:255';
            }
            if (isset($value['description'])) {
                $rules["$key.description"] = 'required|string';
            }
            if (isset($value['chart_type'])) {
                $rules["$key.chart_type"] = 'required|string|in:bar,line,pie';
            }
        }

        // Validasi data
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Inisialisasi HTMLPurifier
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        // Update setiap section
        foreach ($data as $sectionKey => $sectionData) {
            $section = Section::where('section', $sectionKey)->first();
            if ($section) {
                // Sanitasi HTML
                $cleanTitle = isset($sectionData['title']) ? $purifier->purify($sectionData['title']) : $section->title;
                $cleanDescription = isset($sectionData['description']) ? $purifier->purify($sectionData['description']) : $section->description;
                $chartType = isset($sectionData['chart_type']) ? $sectionData['chart_type'] : $section->chart_type;

                // Update section sesuai dengan key
                $section->update([
                    'title' => $cleanTitle,
                    'description' => $cleanDescription,
                    'chart_type' => $chartType, // Update chart_type
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    public function storeSection(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'section' => 'required|string|unique:sections,section|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'chart_type' => 'required|string|in:bar,line,pie',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Inisialisasi HTMLPurifier untuk sanitasi HTML
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        // Sanitasi input
        $cleanTitle = $purifier->purify($request->input('title'));
        $cleanDescription = $purifier->purify($request->input('description'));
        $chartType = $request->input('chart_type');

        // Membuat identifier section unik
        $sectionKey = $request->input('section');

        // Membuat section baru
        try {
            $section = Section::create([
                'section' => $sectionKey,
                'title' => $cleanTitle,
                'description' => $cleanDescription,
                'chart_type' => $chartType,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Section baru berhasil ditambahkan.',
                'section' => $section
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-specific errors
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan section baru.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan section baru.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // app/Http/Controllers/ContentController.php

    public function deleteSection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => 'required|string|exists:sections,section',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $sectionKey = $request->input('section');

        try {
            $section = Section::where('section', $sectionKey)->first();
            if ($section) {
                $section->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Section berhasil dihapus.'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Section tidak ditemukan.'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus section.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
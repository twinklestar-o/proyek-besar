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

        // Definisikan aturan validasi dinamis berdasarkan input
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

                // Update section dengan chart_type
                $section->update([
                    'title' => $cleanTitle,
                    'description' => $cleanDescription,
                    'chart_type' => $chartType,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    }
}

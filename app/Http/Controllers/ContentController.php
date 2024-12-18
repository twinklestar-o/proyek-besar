<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;
use HTMLPurifier;
use HTMLPurifier_Config;
use App\Models\NewSection; // Import the new model

class ContentController extends Controller
{
    public function showPelanggaranView(Request $request)
    {
        // Ambil semua sections dari database
        $sections = Section::all()->keyBy('section');

        // Data filter dan data pelanggaran hanya contoh, sesuaikan dengan logika Anda sendiri
        $filters = $request->all();
        $data = [
            'result' => 'OK',
            'data' => [
                'pelanggaran_per_level' => [
                    1 => 5,
                    2 => 10,
                    3 => 3,
                    4 => 7,
                    5 => 2,
                    6 => 1,
                    7 => 0
                ],
                'total_keseluruhan' => 28
            ]
        ];

        return view('pelanggaran', compact('sections', 'data', 'filters'));
    }

    public function updateNewSections(Request $request)
    {
        $data = $request->all();
        // Validate if description fields are provided
        $rules = [];
        foreach ($data as $key => $value) {
            if (isset($value['description'])) {
                $rules["$key.description"] = 'nullable|string';
            }
        }

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        foreach ($data as $sectionKey => $sectionData) {
            $newSection = NewSection::where('section', $sectionKey)->first();

            if ($newSection) {
                $newSection->update([
                    'description' => isset($sectionData['description']) ? $purifier->purify($sectionData['description']) : $newSection->description,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'New sections updated successfully.'
        ]);
    }


    public function updateSections(Request $request)
    {
        $data = $request->all();

        // Extract deleted sections if any
        $deletedSections = $data['deleted_sections'] ?? [];
        unset($data['deleted_sections']);

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
            if (isset($value['content'])) {
                $rules["$key.content"] = 'nullable|string';
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        foreach ($data as $sectionKey => $sectionData) {
            $section = Section::where('section', $sectionKey)->first();

            if ($section) {
                $cleanTitle = isset($sectionData['title']) ? $purifier->purify($sectionData['title']) : $section->title;
                $cleanDescription = isset($sectionData['description']) ? $purifier->purify($sectionData['description']) : $section->description;
                $chartType = isset($sectionData['chart_type']) ? $sectionData['chart_type'] : $section->chart_type;
                $content = isset($sectionData['content']) ? $purifier->purify($sectionData['content']) : $section->content;

                $section->update([
                    'title' => $cleanTitle,
                    'description' => $cleanDescription,
                    'chart_type' => $chartType,
                    'content' => $content,
                ]);
            } else {
                Section::create([
                    'section' => $sectionKey,
                    'title' => $purifier->purify($sectionData['title']),
                    'description' => $purifier->purify($sectionData['description']),
                    'chart_type' => $sectionData['chart_type'] ?? 'bar',
                    'content' => isset($sectionData['content']) ? $purifier->purify($sectionData['content']) : null,
                ]);
            }
        }

        if (!empty($deletedSections)) {
            Section::whereIn('section', $deletedSections)->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    public function storeNewSection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => 'required|string|unique:new_sections,section|max:255',
            'view_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        $sectionKey = $request->input('section');
        $viewName = $purifier->purify($request->input('view_name'));

        try {
            $newSection = NewSection::create([
                'section' => $sectionKey,
                'view_name' => $viewName,
                'description' => '', // Start with an empty description
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'New section created successfully.',
                'section' => $newSection
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create new section.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



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

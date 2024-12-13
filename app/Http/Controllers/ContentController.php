<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
  public function saveEdits(Request $request)
  {
    $data = $request->all();

    // Simpan data ke file JSON
    $filePath = storage_path('app/public/edited_content.json');
    $saved = file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

    if ($saved) {
      return response()->json(['success' => true]);
    }

    return response()->json(['success' => false], 500);
  }

  public function home()
  {
    $filePath = storage_path('app/public/edited_content.json');
    $editableContent = [];

    if (file_exists($filePath)) {
      $editableContent = json_decode(file_get_contents($filePath), true);
    }

    return view('app.home', compact('editableContent'));
  }


}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    public function download($id)
    {
        $document = Document::findOrFail($id);
        $path = storage_path('app/public/' . $document->file_path);


        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $originalName = $document->original_name ?: basename($document->file_path);

        return response()->download($path, $originalName);
    }
}

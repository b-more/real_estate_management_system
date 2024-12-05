<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function download(Document $document)
    {
        // Check if file exists
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        // Record last access time
        $document->update(['last_accessed_at' => now()]);

        // Get the file content and mime type
        $file = Storage::disk('public')->get($document->file_path);
        $mimeType = Storage::disk('public')->mimeType($document->file_path);

        // Create proper filename with extension
        $filename = $document->title . '.' . $document->file_extension;

        // Return response with proper headers
        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($file),
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Plot;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class PlotDocumentController extends Controller
{
    public function download(Plot $plot)
    {
        $documents = [];
        
        // Add site plan if exists
        if ($plot->site_plan) {
            $documents['site_plan'] = Storage::path('plots/site-plans/' . $plot->site_plan);
        }

        // Add title deed if exists and plot is titled
        if ($plot->legal_status === 'titled' && $plot->title_deed) {
            $documents['title_deed'] = Storage::path('plots/title-deeds/' . $plot->title_deed);
        }

        // Add chief letter if exists and plot is traditional
        if ($plot->legal_status === 'traditional' && $plot->chief_letter) {
            $documents['chief_letter'] = Storage::path('plots/chief-letters/' . $plot->chief_letter);
        }

        if (empty($documents)) {
            return back()->with('error', 'No documents available for this plot.');
        }

        // If only one document, return it directly
        if (count($documents) === 1) {
            $path = reset($documents);
            $filename = basename($path);
            return response()->download($path, $filename);
        }

        // Create a zip file for multiple documents
        $zipFileName = "plot_{$plot->plot_number}_documents.zip";
        $zipPath = storage_path("app/temp/{$zipFileName}");

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($documents as $name => $path) {
                $zip->addFile($path, basename($path));
            }
            $zip->close();

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend();
        }

        return back()->with('error', 'Could not create zip file.');
    }
}
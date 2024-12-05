<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlotDocumentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('plots/{plot}/documents/download', [PlotDocumentController::class, 'download'])
    ->name('plots.documents.download');

Route::get('documents/{document}/download', [DocumentController::class, 'download'])
    ->name('download.document');
<?php

use App\Http\Controllers\VaultUploadController;
use App\Models\VaultDocument;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/vault-documents/upload-encrypted', [VaultUploadController::class, 'upload'])
        ->name('vault.upload.encrypted');

    Route::get('/vault-documents/{document}/encrypted-stream', function (VaultDocument $document) {
        $path = $document->encrypted_file_path;
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Encrypted file not found.');
        }
        return Storage::disk('local')->response(
            $path,
            basename($path),
            ['Content-Type' => 'application/octet-stream']
        );
    })->name('vault.download.encrypted');
});

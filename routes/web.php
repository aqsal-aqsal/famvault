<?php

use App\Models\VaultDocument;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['auth'])->group(function () {
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

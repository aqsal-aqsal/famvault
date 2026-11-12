<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VaultUploadController extends Controller
{
    /**
     * Handle encrypted blob upload directly from client-side Web Crypto API.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'encrypted_blob' => ['required', 'file', 'max:51200'], // max 50MB
        ]);

        $file = $request->file('encrypted_blob');
        $filename = Str::uuid() . '.enc';
        $path = $file->storeAs('vault-documents', $filename, 'local');

        return response()->json([
            'success' => true,
            'path' => $path,
            'filename' => $filename,
        ]);
    }
}

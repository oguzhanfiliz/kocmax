<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    /**
     * Private dosyalara güvenli erişim
     */
    public function show(Request $request, string $path): Response
    {
        try {
            // Dosya yolu güvenlik kontrolü
            if (str_contains($path, '..') || !str_starts_with($path, 'dealer-applications/')) {
                abort(404);
            }
            
            // Dosyanın var olup olmadığını kontrol et
            if (!Storage::disk('private')->exists($path)) {
                abort(404);
            }
            
            // MIME type'ı al
            $mimeType = Storage::disk('private')->mimeType($path);
            
            // Dosya içeriğini al
            $contents = Storage::disk('private')->get($path);
            
            // Response oluştur
            return response($contents, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                'Cache-Control' => 'private, max-age=3600',
            ]);
            
        } catch (\Exception $e) {
            abort(404);
        }
    }
}

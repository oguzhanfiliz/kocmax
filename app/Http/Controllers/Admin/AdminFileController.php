<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AdminFileController extends Controller
{
    /**
     * Filament admin oturumuyla private diskten dosya servis eder.
     */
    public function show(Request $request, string $path): Response
    {
        // Yol güvenliği: sadece dealer-applications klasörüne izin ver
        if (str_contains($path, '..') || !str_starts_with($path, 'dealer-applications/')) {
            abort(404);
        }

        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        $mimeType = Storage::disk('private')->mimeType($path) ?: 'application/octet-stream';
        $contents = Storage::disk('private')->get($path);

        // Admin tarafında genellikle indirme isteği olur; inline yerine attachment tercih edelim
        return response($contents, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}



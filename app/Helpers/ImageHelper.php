<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public static function getImageBase64(?string $path): ?string
    {
        if ($path && Storage::disk('public')->exists($path)) {
            $imageData = Storage::disk('public')->get($path);
            $base64 = base64_encode($imageData);

            return 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.$base64;
        }

        return null;
    }
}

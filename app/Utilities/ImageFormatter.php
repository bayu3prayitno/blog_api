<?php

namespace App\Utilities;

class ImageFormatter
{
    /**
     * Format nama file gambar dengan user ID untuk unikeness
     */
    public static function formatName(string $originalName, int|string $userId): string
    {
        // Dapatkan extension dari file
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // Generate nama file unik: timestamp-userId.extension
        $filename = time() . '-' . $userId . '.' . strtolower($extension);

        return $filename;
    }
}

<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class SignatureHelper
{
    /**
     * Safely get the signature URL from Cloudinary.
     * Returns null if Cloudinary is unreachable (e.g. localhost without internet).
     */
    public static function getSignatureUrl(?string $signatureImage): ?string
    {
        if (!$signatureImage) {
            return null;
        }

        try {
            return Storage::disk('cloudinary')->url($signatureImage);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if Cloudinary is available.
     */
    public static function isCloudinaryAvailable(): bool
    {
        try {
            Storage::disk('cloudinary')->url('test');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

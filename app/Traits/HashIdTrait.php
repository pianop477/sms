<?php
// app/Traits/HashIdTrait.php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait HashIdTrait
{
    /**
     * Encrypt ID for URL
     */
    protected function hashId($id): string
    {
        return Crypt::encryptString((string) $id);
    }

    /**
     * Decrypt ID from URL
     */
    protected function decryptId($hash): ?int
    {
        try {
            return (int) Crypt::decryptString($hash);
        } catch (\Exception $e) {
            return null;
        }
    }
}

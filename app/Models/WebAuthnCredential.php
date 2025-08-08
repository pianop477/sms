<?php

namespace App\Models;

use Laragear\WebAuthn\Models\WebAuthnCredential as BaseModel;

class WebAuthnCredential extends BaseModel  // <-- Hakikisha capitalization sahihi
{
    public function user()
    {
        return $this->belongsTo(User::class, 'authenticatable_id'); // <-- Tumia column sahihi
    }
}

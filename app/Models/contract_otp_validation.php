<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contract_otp_validation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp_code',
        'requested_at',
        'expires_at',
        'verified_at',
        'auth_token',
        'ip_address',
        'token_ttl',
        'is_active',
        'is_used',
        'is_expired',
        'is_verified',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_used' => 'boolean',
        'is_expired' => 'boolean',
        'is_verified' => 'boolean',
        'token_ttl' => 'integer',
    ];

      /**
     * Check if OTP session is valid
     */
    public function isValid(): bool
    {
        return $this->is_verified &&
               !$this->is_used &&
               !$this->is_expired &&
               $this->expires_at > now();
    }

    /**
     * Get applicant details from the stored user_id
     */
    public function getApplicantDetails()
    {
        $controller = app()->make('App\Http\Controllers\ContractController');
        return $controller->resolveApplicantDetails($this->user_id, null);
    }
}

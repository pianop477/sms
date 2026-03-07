<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class school_constracts extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'applicant_id',
        'original_contract_id',
        'new_contract_id',
        'reapplied_at',
        'holder_id',
        'reapply_count',
        'contract_type',
        'job_title',
        'basic_salary',
        'allowances',
        'start_date',
        'end_date',
        'duration',
        'applicant_file_path',
        'applied_at',
        'approved_at',
        'activated_at',
        'contract_file_path',
        'verify_token',
        'qr_code_path',
        'is_active',
        'approved_by',
        'remarks',
        'expired_at',
        'reminder_sent_at',
        'warning_sent_at',
        'rejected_at',
        'terminated_at',
        'status',
    ];

    // In school_constracts.php model
    public function statusHistories()
    {
        return $this->hasMany(ContractStatusHistory::class, 'contract_id');
    }

    public function terminationHistory()
    {
        return $this->hasOne(ContractStatusHistory::class, 'contract_id')
            ->where('new_status', 'terminated')
            ->latest();
    }
}

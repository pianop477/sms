<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportJob extends Model
{
    protected $fillable = [
        'job_id', 'user_id', 'status', 'file_path', 'file_name',
        'total_students', 'processed_students', 'error_message', 'report_type',
        'report_title', 'exam_type', 'month'
    ];
}

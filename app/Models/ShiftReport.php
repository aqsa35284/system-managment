<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShiftReport extends Model
{
    protected $fillable = [
        'user_id',
        'shift_name',
        'total_seconds',
        'ai_percentage',
        'pulse_count',
        'task_summary',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
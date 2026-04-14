<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftReport extends Model
{
    protected $fillable = [
        'user_id', 'shift_name', 'total_seconds', 
        'ai_percentage', 'pulse_count', 'task_summary', 'report_date'
    ];

    // ✅ Yeh line add karein, ye string ko automatically Date object bana degi
    protected $casts = [
        'report_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTimeAttribute()
    {
        $h = floor($this->total_seconds / 3600);
        $m = floor(($this->total_seconds % 3600) / 60);
        return "{$h}h {$m}m";
    }
}
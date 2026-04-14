<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'user_id', 
        'ai_tool', 
        'duration_seconds', 
        'activity_title', 
        'log_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
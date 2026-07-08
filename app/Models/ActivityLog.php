<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id', 
        'description', 'old_values', 'new_values'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Kis user ne activity ki hai
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Dynamic morph relationship
    public function subject()
    {
        return $this->morphTo();
    }
}
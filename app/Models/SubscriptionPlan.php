<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    // JSON column ko array mein convert karne ke liye
    protected $casts = [
        'features' => 'array',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'company_id',
        'invoice_number',
        'plan_name',
        'total_users',
        'total_amount',
        'status',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

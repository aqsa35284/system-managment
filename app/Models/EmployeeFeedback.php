<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFeedback extends Model
{
    use HasFactory;

    protected $table = 'employee_feedbacks';

    // ⭐ FIX: Saare required fields fillable mein hone chahiyein
    protected $fillable = [
        'company_id', 
        'user_id',
        'task_id',
        'update_note',
        'blockers',
        'completion_percentage',
    ];

    // ─────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
     public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
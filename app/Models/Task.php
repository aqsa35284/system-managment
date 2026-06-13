<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ⭐ FIX: VS Code ke error ko khatam karne ke liye tamam models yahan import kar diye hain
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskForward;
use App\Models\EmployeeFeedback;
use App\Models\AdminFeedback;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'project_id',
        'user_id',
        'priority',
        'status',
        'due_date',
        'completion_percentage',
        'completion_stage',
        'days_worked',
        'overdue_days',
    ];

    protected $casts = [
        'due_date'              => 'date',
        'completion_percentage' => 'integer',
        'days_worked'           => 'integer',
        'overdue_days'          => 'integer',
    ];

    // ─────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forwards()
    {
        return $this->hasMany(TaskForward::class)->with(['fromUser', 'toUser']);
    }

    public function employeeUpdates()
    {
        return $this->hasMany(EmployeeFeedback::class)->with('user')->latest();
    }

    public function adminFeedbacks()
    {
        return $this->hasMany(AdminFeedback::class);
    }
}
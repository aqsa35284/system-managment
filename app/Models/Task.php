<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'priority', 
        'status', 
        'due_date', 
        'project_id', 
        'user_id'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relationship with Project
    public function project() {
        return $this->belongsTo(Project::class);
    }

    // Relationship with User (Employee)
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function adminFeedbacks() {
    return $this->hasMany(AdminFeedback::class, 'task_id');
}

public function employeeUpdates() {
    return $this->hasMany(EmployeeFeedback::class, 'task_id');
}
}

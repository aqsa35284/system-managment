<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFeedback extends Model
{
    protected $table = 'employee_feedbacks';

    protected $fillable = [
        'user_id', 
        'task_id', 
        'update_note', 
        'blockers', 
        'completion_percentage'
    ];

    // Update dene wala Employee
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
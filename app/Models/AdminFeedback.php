<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminFeedback extends Model
{
    protected $table = 'admin_feedbacks'; // Table name specify kar diya

    protected $fillable = [
        'admin_id', 
        'employee_id', 
        'task_id', 
        'review_note', 
        'rating_score', 
        'status'
    ];

    // Feedback dene wala Admin
    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Jis ko feedback mila (Employee)
    public function employee() {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
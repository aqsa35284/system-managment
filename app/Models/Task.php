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

    // --- 🏗️ RELATIONSHIPS ---

    /**
     * Relationship with Project
     * Ek task kisi ek project ka hissa hoti hai.
     */
    public function project() {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relationship with User (Operator/Employee)
     * Yeh operator hai jo is task par kaam kar raha hai.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin Feedbacks (Full History)
     * Agar aapko task ki saari purani reviews dekhni hon.
     */
    public function adminFeedbacks() {
        return $this->hasMany(AdminFeedback::class, 'task_id');
    }

    /**
     * Latest Admin Feedback (Dashboard logic)
     * Dashboard par sirf sab se naya review dikhane ke liye.
     */
    public function adminFeedback() {
        return $this->hasOne(AdminFeedback::class, 'task_id')->latestOfMany();
    }

    /**
     * Employee Updates
     * Employee jo dashboard se "Update Intel" bhejta hai.
     */
    public function employeeUpdates() {
        return $this->hasMany(EmployeeFeedback::class, 'task_id');
    }
}
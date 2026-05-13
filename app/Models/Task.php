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
        'user_id',           // ✅ Current assigned employee
        'original_user_id',  // ✅ Pehla owner (track ke liye)
        'forwarded_to',      // ✅ Last forward kiska ID tha
        'completion_percentage', // ✅ Progress %
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // =========================================================
    // 🏗️ CORE RELATIONSHIPS
    // =========================================================

    /**
     * Project se relation
     * Ek task kisi ek project ka hissa hoti hai.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Current assigned employee
     * Jo abhi task par kaam kar raha hai.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Original task owner
     * Task pehle kisne banaya / pehle kise assign hua tha.
     */
    public function originalUser()
    {
        return $this->belongsTo(User::class, 'original_user_id');
    }

    /**
     * Last person ko forward kiya gaya
     * Sirf last forwarded user — history ke liye forwards() use karo.
     */
    public function forwardedToUser()
    {
        return $this->belongsTo(User::class, 'forwarded_to');
    }

    // =========================================================
    // 🔄 FORWARD HISTORY RELATIONSHIPS
    // =========================================================

    /**
     * Poori forward chain (admin ke liye)
     * Har baar kisi ne forward kiya — sab records.
     */
    public function forwards()
    {
        return $this->hasMany(TaskForward::class)->with(['fromUser', 'toUser'])->latest();
    }

    /**
     * Sirf latest forward record
     */
    public function latestForward()
    {
        return $this->hasOne(TaskForward::class)->latestOfMany();
    }

    // =========================================================
    // 📋 FEEDBACK / REVIEW RELATIONSHIPS
    // =========================================================

    /**
     * Admin ki saari reviews (full history)
     */
    public function adminFeedbacks()
    {
        return $this->hasMany(AdminFeedback::class, 'task_id');
    }

    /**
     * Admin ki sirf latest review (dashboard ke liye)
     */
    public function adminFeedback()
    {
        return $this->hasOne(AdminFeedback::class, 'task_id')->latestOfMany();
    }

    /**
     * Employee ke saare "Update Intel" logs
     */
    public function employeeUpdates()
    {
        return $this->hasMany(EmployeeFeedback::class, 'task_id');
    }

    // =========================================================
    // 🛠️ HELPER METHODS
    // =========================================================

    /**
     * Check karo kya task forward hua hai kabhi
     */
    public function wasForwarded(): bool
    {
        return !is_null($this->forwarded_to) || !is_null($this->original_user_id);
    }

    /**
     * Forward count — kitni baar forward hua
     */
    public function forwardCount(): int
    {
        return $this->hasMany(TaskForward::class)->count();
    }
}

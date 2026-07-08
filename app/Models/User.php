<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivity; // 🟢 1. LOGS ACTIVITY IMPORT KI GAYI HAI

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity; // 🟢 2. YAHAN TRAIT ADD KI GAYI HAI

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'designation',
        'salary',
        'department_id',
        'join_date',
        'profile_image',
        'status',
        'screenshot_trigger',   // ← یہ ضروری ہے
        'company_id',           // 🚀 YEH SAB SE ZAROORI HAI (Isi se login theek hoga)
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'password'           => 'hashed',
        'screenshot_trigger' => 'boolean',
    ];

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function screenshots()
    {
        return $this->hasMany(Screenshot::class);
    }

    public function aiUsageLogs()
    {
        return $this->hasMany(AiUsageLog::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
 
    // Inside User model add this relationship
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    // Agar user 'assistant_admin' hai toh wo kin companies ko manage karta hai
    public function managedCompanies()
    {
        return $this->belongsToMany(Company::class, 'company_assistant', 'user_id', 'company_id');
    }
    
    // YEH RELATIONSHIP LAZMI HONI CHAHIYE
    public function companies()
    {
        return $this->hasMany(Company::class, 'partner_id');
    }
}
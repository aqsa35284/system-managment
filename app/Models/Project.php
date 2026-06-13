<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ⭐ FIX: VS Code ke errors se bachne ke liye imports
use App\Models\User;
use App\Models\Task;
use App\Models\Company;

class Project extends Model
{
    use HasFactory;

    // ⭐ FIX: company_id ko shuru mein add kar diya gaya hai
    protected $fillable = [
        'company_id',
        'name', 
        'description', 
        'start_date', 
        'deadline', 
        'status', 
        'client_name', 
        'budget'
    ];

    /**
     * Dates ko Carbon instances mein convert karein
     * Taaki aap {{ $project->deadline->format('d M, Y') }} asani se use kar saken
     */
    protected $casts = [
        'start_date' => 'date',
        'deadline'   => 'date',
    ];

    // ─────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────

    /**
     * ⭐ Nayi Relation: Project kis company ka hai
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relationship: Aik project mein kai employees ho sakte hain.
     * User model ke saath Many-to-Many connection
     */
    public function employees()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->withTimestamps(); // Pivot table ke timestamps update karne ke liye
    }

    /**
     * Project aur Tasks ka relationship (One to Many)
     * Aik project ke under bohat saare tasks aate hain
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
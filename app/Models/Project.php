<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // 'Model' class ko sahi import karein

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
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

    /**
     * Relationship: Aik project mein kai employees ho sakte hain.
     * User model ke saath Many-to-Many connection
     */
    public function employees()
    {
        // 'User' class ko link karein
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->withTimestamps(); // Pivot table ke timestamps update karne ke liye
    }
}
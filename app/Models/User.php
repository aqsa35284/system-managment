<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Department; // ⭐ Import the Department model

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
    'name',
    'username',
    'email',
    'password',
    'role',
    'phone',
    'designation',
    'salary',          // ⭐ Yeh MISSING tha, isay add karein
    'department_id',
    'join_date',
    'profile_image',
    'status',
];

    // ... (Hidden aur Casts waala code)

    /**
     * Relationship: User belongs to a Department.
     */
    public function department()
    {
        // Ab yahan 'Department' class pehchani jayegi
        return $this->belongsTo(Department::class);
    }
    public function projects() {
    return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
}
public function tasks()
{
    // Aik project mein bohat saare tasks ho sakte hain
    return $this->hasMany(Task::class);
}
}
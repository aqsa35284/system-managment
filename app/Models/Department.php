<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * ⭐ Fillable Attributes: In fields mein data save karne ki ijazat hai.
     */
    protected $fillable = [
        'name',             // e.g., IT Department
        'department_code',  // e.g., IT-01
    ];

    /**
     * ⭐ Relationship: A Department has many Users (Employees).
     * Is se aap check kar sakti hain ke aik department mein kitne log hain.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
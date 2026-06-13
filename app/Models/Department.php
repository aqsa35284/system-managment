<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany; // ⭐ 1. TRAIT YAHAN IMPORT KAREIN

class Department extends Model
{
    // ⭐ 2. TRAIT KO YAHAN USE KAREIN (HasFactory ke sath)
    use HasFactory, BelongsToCompany; 

    /**
     * ⭐ Fillable Attributes: In fields mein data save karne ki ijazat hai.
     */
    protected $fillable = [
        'company_id', 
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
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
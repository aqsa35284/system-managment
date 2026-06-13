<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'status', 'payment_status', 
        'subscription_ends_at', 'max_employees','plan_type'
    ];

    // Ek company ke bohot se users (admins/employees) hote hain
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Custom check: Kya subscription valid hai?
    public function isValid()
    {
        return $this->status === 'active' && $this->subscription_ends_at >= now();
    }
   

    // Ek company ke bohot se employees ho sakte hain
    public function employees()
    {
        return $this->hasMany(User::class, 'company_id')->where('role', 'employee');
    }

    // Ek company ke admins
    public function admins()
    {
        return $this->hasMany(User::class, 'company_id')->where('role', 'admin');
    }
}
    

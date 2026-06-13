<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'company_id', 
        'user_id', 
        'date', 
        'check_in', 
        'check_out', 
        'work_time', // ⭐ Added: Seconds store karne ke liye
        'shift', 
        'total_hours', 
        'status' // present, idle, late, etc.
    ];

    /**
     * Dates ko Carbon instances mein cast karna.
     */
    protected $casts = [
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
        'date'      => 'date',
        'work_time' => 'integer', // Cast to integer for easy calculation
    ];

    /**
     * Relationship: Attendance belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper: Time ke mutabiq Shift ka naam nikalne ke liye.
     */
    public static function getShiftName($time)
    {
        $hour = Carbon::parse($time)->format('H');

        if ($hour >= 6 && $hour < 14) {
            return 'Morning';
        } elseif ($hour >= 14 && $hour < 22) {
            return 'Evening';
        } else {
            return 'Night';
        }
    }

    /**
     * Scope: Aaj ki active attendance nikalne ke liye helper.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('check_out')
                     ->whereDate('date', Carbon::today());
    }
     public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
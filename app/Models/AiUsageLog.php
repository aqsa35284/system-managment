<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ai_tool',
        'log_date',
        'activity_title',
        'duration_seconds',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForPeriod($query, string $period = 'today')
    {
        $tz = 'Asia/Karachi';
        return match ($period) {
            'weekly'  => $query->whereBetween('log_date', [
                            now($tz)->startOfWeek()->toDateString(),
                            now($tz)->endOfWeek()->toDateString(),
                         ]),
            'monthly' => $query->whereMonth('log_date', now($tz)->month)
                               ->whereYear('log_date', now($tz)->year),
            'yearly'  => $query->whereYear('log_date', now($tz)->year),
            default   => $query->whereDate('log_date', now($tz)->toDateString()),
        };
    }
}
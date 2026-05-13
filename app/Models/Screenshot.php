<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
    protected $fillable = [
        'user_id',
        'image_path',
        'window_title',
        'triggered_by_admin',
    ];

    protected $casts = [
        'triggered_by_admin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
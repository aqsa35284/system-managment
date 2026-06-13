<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
    protected $fillable = [
        'company_id', 
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
     public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
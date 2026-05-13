<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    // ⭐ FIX: file_path, file_name, or voice_path ko fillable mein add karna zaroori hai
    protected $fillable = [
        'sender_id', 
        'receiver_id', 
        'group_name', 
        'message', 
        'file_path', 
        'file_name', 
        'voice_path', 
        'message_type', 
        'is_read'
    ];
    
    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
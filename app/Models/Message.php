<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    protected $fillable = ['sender_id', 'receiver_id', 'group_name', 'message', 'message_type', 'is_read'];
    
    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScreenAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $employeeName;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($employeeName, $message)
    {
        $this->employeeName = $employeeName;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // Public channel jis par admin dashboards listen kar rahe hain
        return new Channel('admin-alerts');
    }

    /**
     * Broadcast as a customized action name.
     */
    public function broadcastAs()
    {
        return 'new-alert';
    }
}
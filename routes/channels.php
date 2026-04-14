<?php
use Illuminate\Support\Facades\Broadcast;

// Personal Chat Channel
Broadcast::channel('chat.user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Group/Department Chat Channel (Agar aap use kar rahe hain)
Broadcast::channel('chat.dept.{id}', function ($user, $id) {
    // Check karein agar user us department ka hissa hai
    return (int) $user->department_id === (int) $id;
});

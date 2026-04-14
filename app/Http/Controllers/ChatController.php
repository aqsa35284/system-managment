<?php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index() {
        $users = User::where('id', '!=', Auth::id())->get();
        $active_user_id = null; // Default null taake page load par error na aaye
        return view('chat.index', compact('users', 'active_user_id'));
    }

    public function sendMessage(Request $request) {
        $request->validate(['message' => 'required']);

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'group_name'  => $request->group_name ?? null,
            'message'     => $request->message,
            'message_type'=> 'text',
            'is_read'     => 0
        ]);

        return response()->json(['success' => true, 'message' => $message->load('sender')]);
    }

    public function fetchMessages(Request $request) {
        $query = Message::with('sender');
        if ($request->group_name) {
            $query->where('group_name', $request->group_name);
        } else {
            $id = $request->receiver_id;
            if (!$id) return response()->json([]);
            $query->where(function($q) use ($id) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $id);
            })->orWhere(function($q) use ($id) {
                $q->where('sender_id', $id)->where('receiver_id', Auth::id());
            });
        }
        return response()->json($query->oldest()->get());
    }
}
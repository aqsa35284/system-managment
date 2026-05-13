<?php
namespace App\Http\Controllers;

use App\Models\{Message, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller 
{
    public function index() {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat.index', compact('users'));
    }

    public function sendMessage(Request $request) {
        $filePath = null;
        $fileName = null;
        $voicePath = null;
        $type = 'text';

        // ⭐ File Handling (Ab extension change nahi hogi)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Asli extension ke sath save karne ke liye naya naam banaya
            $fileNameToStore = time() . '_' . uniqid() . '.' . $extension;
            
            // store() ki bajaye storeAs() use kiya hai
            $filePath = $file->storeAs('chat_files', $fileNameToStore, 'public');
            
            $fileName = $originalName;
            $type = 'file';
        }

        // ⭐ Voice Note Handling (Proper .webm format mein save hogi)
        if ($request->hasFile('voice')) {
            $fileNameToStore = time() . '_' . uniqid() . '.webm';
            $voicePath = $request->file('voice')->storeAs('chat_voice', $fileNameToStore, 'public');
            $type = 'voice';
        }

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id ?: null,
            'group_name'  => $request->group_name ?: null,
            'message'     => $request->message,
            'file_path'   => $filePath,
            'file_name'   => $fileName,
            'voice_path'  => $voicePath,
            'message_type'=> $type,
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
            
            // ⭐ NAYA FIX: Jab chat open ho aur messages fetch hon, toh unhe 'Read' mark kar do
            Message::where('sender_id', $id)
                   ->where('receiver_id', Auth::id())
                   ->where('is_read', false)
                   ->update(['is_read' => true]);
            
            $query->where(function($q) use ($id) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $id);
            })->orWhere(function($q) use ($id) {
                $q->where('sender_id', $id)->where('receiver_id', Auth::id());
            });
        }
        return response()->json($query->oldest()->get());
    }

    // ⭐ NAYA FUNCTION FIX: Unread messages count aur Sender Name (Notification Badge/Toast ke liye)
    public function getUnreadCount() {
        // Sab se aakhri unread message nikalna taake sender ka naam mil sake
        $latestUnread = Message::with('sender')
                        ->where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->latest()
                        ->first();

        // Total unread messages ki ginti
        $count = Message::where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                        
        return response()->json([
            'count' => $count,
            'sender_name' => $latestUnread ? $latestUnread->sender->name : 'System'
        ]);
    }

    // WhatsApp style delete message logic
    public function deleteMessage($id) {
        // Check if message exists and belongs to the logged-in user (Security Check)
        $msg = Message::where('id', $id)->where('sender_id', Auth::id())->first();
        
        if ($msg) {
            // Agar message mein image, document ya voice hai, toh server se permanently delete karein
            if ($msg->file_path) {
                Storage::disk('public')->delete($msg->file_path);
            }
            if ($msg->voice_path) {
                Storage::disk('public')->delete($msg->voice_path);
            }
            
            // Database se delete karein
            $msg->delete();
            
            return response()->json(['success' => true, 'message' => 'Message deleted for everyone']);
        }
        
        return response()->json(['success' => false, 'message' => 'Unauthorized or Not Found'], 403);
    }

    // Force download file with original extension (Rokta hai .txt banne se)
    public function downloadFile($id) {
        $msg = Message::findOrFail($id);
        
        // Check karein file hai ya voice note
        $path = $msg->file_path ?: $msg->voice_path;
        
        if ($path && Storage::disk('public')->exists($path)) {
            // Asli naam set karein
            $downloadName = $msg->file_name ?: 'VoiceNote_' . $msg->id . '.webm';
            
            // Laravel ka force download function
            return Storage::disk('public')->download($path, $downloadName);
        }
        
        return abort(404, 'File not found on server.');
    }
}
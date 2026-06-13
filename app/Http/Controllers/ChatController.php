<?php
namespace App\Http\Controllers;

use App\Models\{Message, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller 
{
    // ─────────────────────────────────────────────────────────────
    // INDEX — Load Users (Isolated by Company)
    // ─────────────────────────────────────────────────────────────
    public function index() {
        // ⭐ FIX: Sirf apni company ke users fetch honge
        $users = User::where('company_id', Auth::user()->company_id)
                     ->where('id', '!=', Auth::id())
                     ->get();
                     
        return view('chat.index', compact('users'));
    }

    // ─────────────────────────────────────────────────────────────
    // SEND MESSAGE (With Security Check)
    // ─────────────────────────────────────────────────────────────
    public function sendMessage(Request $request) {
        
        // ⭐ FIX: Security Check - Ensure receiver is from the same company
        if ($request->receiver_id) {
            $validReceiver = User::where('id', $request->receiver_id)
                                 ->where('company_id', Auth::user()->company_id)
                                 ->exists();
                                 
            if (!$validReceiver) {
                return response()->json(['success' => false, 'message' => 'Unauthorized transmission.'], 403);
            }
        }

        $filePath = null;
        $fileName = null;
        $voicePath = null;
        $type = 'text';

        // File Handling
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            $fileNameToStore = time() . '_' . uniqid() . '.' . $extension;
            $filePath = $file->storeAs('chat_files', $fileNameToStore, 'public');
            
            $fileName = $originalName;
            $type = 'file';
        }

        // Voice Note Handling
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

    // ─────────────────────────────────────────────────────────────
    // FETCH MESSAGES (Isolated by Company)
    // ─────────────────────────────────────────────────────────────
    public function fetchMessages(Request $request) {
        $query = Message::with('sender');
        
        if ($request->group_name) {
            // Agar group chat system use kar rahe hain, toh uski details yahan aayengi
            $query->where('group_name', $request->group_name);
        } else {
            $id = $request->receiver_id;
            if (!$id) return response()->json([]);
            
            // ⭐ FIX: Verify receiver belongs to the same company before loading chat
            $validReceiver = User::where('id', $id)
                                 ->where('company_id', Auth::user()->company_id)
                                 ->exists();
            if (!$validReceiver) return response()->json([]);
            
            // Mark as Read
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

    // ─────────────────────────────────────────────────────────────
    // UNREAD COUNT
    // ─────────────────────────────────────────────────────────────
    public function getUnreadCount() {
        $latestUnread = Message::with('sender')
                        ->where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->latest()
                        ->first();

        $count = Message::where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                        
        return response()->json([
            'count' => $count,
            'sender_name' => $latestUnread ? $latestUnread->sender->name : 'System'
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE MESSAGE
    // ─────────────────────────────────────────────────────────────
    public function deleteMessage($id) {
        $msg = Message::where('id', $id)->where('sender_id', Auth::id())->first();
        
        if ($msg) {
            if ($msg->file_path) {
                Storage::disk('public')->delete($msg->file_path);
            }
            if ($msg->voice_path) {
                Storage::disk('public')->delete($msg->voice_path);
            }
            
            $msg->delete();
            return response()->json(['success' => true, 'message' => 'Message deleted for everyone']);
        }
        
        return response()->json(['success' => false, 'message' => 'Unauthorized or Not Found'], 403);
    }

    // ─────────────────────────────────────────────────────────────
    // DOWNLOAD FILE (With Access Security)
    // ─────────────────────────────────────────────────────────────
    public function downloadFile($id) {
        $msg = Message::findOrFail($id);
        
        // ⭐ FIX: Security check - Sirf Sender ya Receiver file download kar sakta hai
        if ($msg->sender_id !== Auth::id() && $msg->receiver_id !== Auth::id()) {
            abort(403, 'Unauthorized Data Access Detected.');
        }
        
        $path = $msg->file_path ?: $msg->voice_path;
        
        if ($path && Storage::disk('public')->exists($path)) {
            $downloadName = $msg->file_name ?: 'VoiceNote_' . $msg->id . '.webm';
            return Storage::disk('public')->download($path, $downloadName);
        }
        
        return abort(404, 'File not found on server.');
    }
}
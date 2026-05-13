<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected $timezone = 'Asia/Karachi';

    /**
     * Display Attendance Logs
     */
    public function index()
    {
        // Variable name 'attendances' use kiya hai taake aapki blade file se match kare
        $attendances = Attendance::where('user_id', Auth::id())
            ->orderByDesc('date')
            ->orderByDesc('check_in')
            ->paginate(15);

       return view('employee.attendance.index', compact('attendances'));
    }

    /**
     * Handle Check-In (Advanced Logic)
     */
    public function checkIn()
    {
        $today = Carbon::today($this->timezone);
        $now   = Carbon::now($this->timezone);
        $userId = Auth::id();

        // ✅ FIX: Pehle check karo ke kya aaj ka koi active session (Check-out null) pehle se mojud hai?
        $existingSession = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->first();

        if ($existingSession) {
            // Agar session mojud hai toh naya create na karo, usi ko continue karo
            return response()->json([
                'success' => true, 
                'message' => 'Session restored', 
                'work_time' => $existingSession->work_time
            ]);
        }

        // Agar koi active session nahi hai, tab naya create karo
        Attendance::create([
            'user_id'   => $userId,
            'date'      => $today->toDateString(),
            'check_in'  => $now->format('H:i:s'),
            'work_time' => 0,
            'status'    => 'present', // Status add kar diya
        ]);

        return response()->json(['success' => true, 'message' => 'New session started']);
    }

    /**
     * Handle Check-Out
     */
    public function checkOut()
    {
        $today = Carbon::today($this->timezone);
        $userId = Auth::id();

        // Sirf wahi record update karo jo aaj ka hai aur check_out null hai
        $att = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->latest()
            ->first();

        if ($att) {
            $att->update([
                'check_out' => Carbon::now($this->timezone)->format('H:i:s'),
                'status'    => 'offline',
            ]);
            return response()->json(['success' => true, 'message' => 'Checked out successfully']);
        }

        return response()->json(['success' => false, 'message' => 'No active session found']);
    }

    /**
     * Silent Sync for Work Time (Backend reinforcement)
     */
    public function reportIdle(Request $request)
    {
        // Agar aap future mein idle time detect karna chahen toh yahan logic aayegi
        return response()->json(['ok' => true]);
    }
}
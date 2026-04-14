<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display Personal Attendance History
     */
    public function index()
    {
        $user = Auth::user();
        
        // Latest history with pagination
        $attendances = Attendance::where('user_id', $user->id)
                                 ->orderBy('date', 'desc')
                                 ->paginate(10);

        return view('employee.attendance.index', compact('attendances', 'user'));
    }

    /**
     * 🟢 CHECK-IN LOGIC
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $activeSession = Attendance::where('user_id', $user->id)
                                    ->whereNull('check_out')
                                    ->whereDate('date', $today)
                                    ->first();

        if ($activeSession) {
            return response()->json([
                'success' => true, // Success isliye taake dashboard lock na ho
                'message' => 'Node connection resumed.'
            ]);
        }

        $now = Carbon::now();
        
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $now,
            'shift' => Attendance::getShiftName($now),
            'status' => 'present',
            'work_time' => 0 // Initial seconds
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'System Access Granted. Shift: ' . $attendance->shift
        ]);
    }

    /**
     * 🔴 CHECK-OUT LOGIC
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereNull('check_out')
                                ->latest()
                                ->first();

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'No active session detected.']);
        }

        $checkOut = Carbon::now();
        
        // ⭐ Precise Hours Calculation from work_time (seconds)
        // Kyunki hum timer sync kar rahe hain, toh work_time hi accurate hours dega
        $totalHours = $attendance->work_time / 3600;

        $attendance->update([
            'check_out' => $checkOut,
            'total_hours' => round($totalHours, 2),
            'status' => 'offline'
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Session Terminated. Final Hours: ' . round($totalHours, 2)
        ]);
    }

    /**
     * 🛰️ SILENT MONITORING LOGIC (Idle/Present)
     */
    public function reportIdle(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())
                                ->whereNull('check_out')
                                ->whereDate('date', Carbon::today())
                                ->first();

        if ($attendance) {
            // Sirf status update karega (idle ya present)
            $attendance->update([
                'status' => $request->status 
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
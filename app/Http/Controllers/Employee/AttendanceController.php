<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ⭐ Global Timezone
    protected $timezone = 'Asia/Karachi';

    // ─────────────────────────────────────────────────────────────
    // ATTENDANCE INDEX
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $attendances = Attendance::where('user_id', Auth::id())
            ->orderByDesc('date')
            ->orderByDesc('check_in')
            ->paginate(15);

        // ⭐ Fixed view path
        return view('employee.attendance.index', compact('attendances'));
    }

    // ─────────────────────────────────────────────────────────────
    // CHECK IN
    // ─────────────────────────────────────────────────────────────
    public function checkIn()
    {
        $today  = Carbon::today($this->timezone);
        $now    = Carbon::now($this->timezone);
        $userId = Auth::id();

        // ⭐ Check existing active session
        $existingSession = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->latest()
            ->first();

        // ⭐ Restore existing session
        if ($existingSession) {

            return response()->json([
                'success'   => true,
                'message'   => 'Session restored',
                'work_time' => $existingSession->work_time
            ]);
        }

        // ⭐ Create new attendance session
        Attendance::create([
            'user_id'   => $userId,
            'date'      => $today->toDateString(),
            'check_in'  => $now->format('H:i:s'),
            'work_time' => 0,
            'status'    => 'present',
            'created_at'=> $now,
            'updated_at'=> $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'New session started'
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // CHECK OUT
    // ─────────────────────────────────────────────────────────────
    public function checkOut()
    {
        $today  = Carbon::today($this->timezone);
        $now    = Carbon::now($this->timezone);
        $userId = Auth::id();

        // ⭐ Find active session only
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->latest()
            ->first();

        if ($attendance) {

            $attendance->update([
                'check_out' => $now->format('H:i:s'),
                'status'    => 'offline',
                'updated_at'=> $now,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checked out successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No active session found'
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // REPORT IDLE
    // ─────────────────────────────────────────────────────────────
    public function reportIdle(Request $request)
    {
        $today  = Carbon::today($this->timezone);
        $now    = Carbon::now($this->timezone);
        $userId = Auth::id();

        // ⭐ Update current active session time
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->latest()
            ->first();

        if ($attendance) {

            $attendance->update([
                'work_time' => (int) (
                    $request->seconds ?? $attendance->work_time
                ),

                'updated_at' => $now,
            ]);
        }

        return response()->json([
            'ok' => true
        ]);
    }
}
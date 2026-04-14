<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Admin Fleet Monitoring - Full Records with Filters
     */
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        // 🔍 1. Filter by Specific Date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // 🔍 2. Filter by Month
        if ($request->filled('month')) {
            $month = Carbon::parse($request->month)->month;
            $year = Carbon::parse($request->month)->year;
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        // 🔍 3. Filter by Year
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // 🔍 4. Filter by Operator (User)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 🔍 5. Filter by Shift
        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        // Clone query for stats calculation before pagination
        $statsQuery = clone $query;

        // Sorting: Sabse naya session sabse upar
        $attendances = $query->latest('date')->latest('check_in')->paginate(20);
        
        // Fleet selection dropdown ke liye
        $employees = User::where('role', 'employee')->get();

        // 📊 🚀 ACCURATE STATS FIX:
        // Sum work_time (seconds) instead of total_hours for real-time accuracy
        $totalSeconds = $statsQuery->sum('work_time');
        $calculatedHours = $totalSeconds / 3600;

        // ✨ FIX: Active Now logic should follow the current date for accuracy
        $activeNow = Attendance::whereNull('check_out')
            ->whereDate('date', Carbon::today())
            ->count();

        $stats = [
            'total_hours'    => round($calculatedHours, 2),
            'total_sessions' => $statsQuery->count(),
            'active_now'     => $activeNow // Yeh ab zero nahi dikhayega agar koi active hai
        ];

        return view('admin.attendance.index', compact('attendances', 'employees', 'stats'));
    }

    /**
     * Delete record if needed
     */
    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return back()->with('success', 'Operational log purged successfully.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Admin Fleet Monitoring - Grouped Records with Shift-wise Intel
     */
    public function index(Request $request)
    {
        // 1. Initial Query for Overall Stats (Un-grouped for accurate sums)
        $statsQuery = Attendance::query();

        //  Apply Base Filters for Stats
        if ($request->filled('date')) {
            $statsQuery->whereDate('date', $request->date);
        }
        if ($request->filled('month')) {
            $monthDate = Carbon::parse($request->month);
            $statsQuery->whereMonth('date', $monthDate->month)->whereYear('date', $monthDate->year);
        }
        if ($request->filled('user_id')) {
            $statsQuery->where('user_id', $request->user_id);
        }

        // 2.  ADVANCED GROUPED LOGIC: Har operator ki aik din ki shift-wise report
        // Pakistani Waqt ke mutabiq shifts divide ki gayi hain
        $groupedQuery = Attendance::with(['user.department'])
            ->select(
                'user_id',
                'date',
                //  Morning Shift (05:00 to 11:59)
                DB::raw("SUM(CASE WHEN HOUR(check_in) >= 5 AND HOUR(check_in) < 12 THEN work_time ELSE 0 END) as morning_seconds"),
                //  Afternoon Shift (12:00 to 16:59)
                DB::raw("SUM(CASE WHEN HOUR(check_in) >= 12 AND HOUR(check_in) < 17 THEN work_time ELSE 0 END) as afternoon_seconds"),
                //  Evening/Night Shift (17:00 to 04:59) - Handles midnight crossing
                DB::raw("SUM(CASE WHEN HOUR(check_in) >= 17 OR HOUR(check_in) < 5 THEN work_time ELSE 0 END) as night_seconds"),
                // Totals & Integrity
                DB::raw('SUM(work_time) as total_work_seconds'),
                DB::raw('MIN(check_in) as first_entry'),
                DB::raw('MAX(check_out) as last_exit'),
                DB::raw('COUNT(*) as total_sessions'), // ⭐ Login Frequency
                DB::raw('MAX(status) as current_integrity')
            )
            // Re-apply Filters to the grouped results
            ->when($request->date, fn($q) => $q->whereDate('date', $request->date))
            ->when($request->month, function($q) use ($request) {
                $m = Carbon::parse($request->month);
                return $q->whereMonth('date', $m->month)->whereYear('date', $m->year);
            })
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->groupBy('user_id', 'date')
            ->orderBy('date', 'desc')
            ->orderBy('first_entry', 'desc');

        $attendances = $groupedQuery->paginate(20)->withQueryString();

        // 3. 📊 ACCURATE INTEL CALCULATIONS
        $totalSeconds = $statsQuery->sum('work_time');
        $calculatedHours = $totalSeconds / 3600;

        // Today's Real-time Active Signal Check
        $activeNow = Attendance::whereNull('check_out')
            ->whereDate('date', Carbon::today())
            ->distinct('user_id')
            ->count();

        $stats = [
            'total_hours'    => round($calculatedHours, 2),
            'total_sessions' => $statsQuery->count(), // Total logins count
            'active_now'     => $activeNow,
            'filtered_count' => $attendances->total()
        ];

        // Fleet selection for dropdown filter
        $employees = User::where('role', 'employee')->get();

        return view('admin.attendance.index', compact('attendances', 'employees', 'stats'));
    }

    /**
     * Delete operational log record
     */
    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return back()->with('success', 'Operational log entry purged successfully.');
    }
}
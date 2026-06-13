<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // ⭐ Auth facade added

class AttendanceController extends Controller
{
    /**
     * Admin Fleet Monitoring - Grouped Records with Shift-wise Intel & Distraction Deductions
     */
    public function index(Request $request)
    {
        $timezone = 'Asia/Karachi';
        $today = Carbon::today($timezone);
        $company_id = Auth::user()->company_id; // ⭐ User's company ID extracted

        // 1. Initial Queries Setup (⭐ FIX: Restricted to current company via whereHas)
        $statsQuery = Attendance::whereHas('user', function ($q) use ($company_id) {
            $q->where('company_id', $company_id);
        });
        
        $groupedQuery = Attendance::with(['user.department'])
            ->whereHas('user', function ($q) use ($company_id) {
                $q->where('company_id', $company_id);
            });

        // 2. HANDLED TIMELINES: Daily, Weekly, Monthly, Yearly Filters
        if ($request->filled('date')) {
            $statsQuery->whereDate('date', $request->date);
            $groupedQuery->whereDate('attendances.date', $request->date);
        } elseif ($request->filled('month')) {
            $monthDate = Carbon::parse($request->month);
            $statsQuery->whereMonth('date', $monthDate->month)->whereYear('date', $monthDate->year);
            $groupedQuery->whereMonth('attendances.date', $monthDate->month)->whereYear('attendances.date', $monthDate->year);
        } else {
            $period = $request->get('period', 'today');
            
            switch ($period) {
                case 'today':
                    $statsQuery->whereDate('date', $today->toDateString());
                    $groupedQuery->whereDate('attendances.date', $today->toDateString());
                    break;
                case 'weekly':
                    $startOfWeek = Carbon::now($timezone)->startOfWeek()->toDateString();
                    $endOfWeek = Carbon::now($timezone)->endOfWeek()->toDateString();
                    $statsQuery->whereBetween('date', [$startOfWeek, $endOfWeek]);
                    $groupedQuery->whereBetween('attendances.date', [$startOfWeek, $endOfWeek]);
                    break;
                case 'monthly':
                    $statsQuery->whereMonth('date', $today->month)->whereYear('date', $today->year);
                    $groupedQuery->whereMonth('attendances.date', $today->month)->whereYear('attendances.date', $today->year);
                    break;
                case 'yearly':
                    $statsQuery->whereYear('date', $today->year);
                    $groupedQuery->whereYear('attendances.date', $today->year);
                    break;
            }
        }

        // Apply User Filter if selected
        if ($request->filled('user_id')) {
            $statsQuery->where('user_id', $request->user_id);
            $groupedQuery->where('attendances.user_id', $request->user_id);
        }

        // 3. SUBQUERY FOR DISTRACTIONS (⭐ FIX: Joined with users to ensure company isolation)
        $distractionSubquery = DB::table('ai_usage_logs')
            ->join('users', 'ai_usage_logs.user_id', '=', 'users.id')
            ->select('ai_usage_logs.user_id', 'ai_usage_logs.log_date', DB::raw('SUM(ai_usage_logs.duration_seconds) as total_distraction_seconds'))
            ->where('ai_usage_logs.ai_tool', '!=', 'IDLE')
            ->where('users.company_id', $company_id) // Isolation check
            ->groupBy('ai_usage_logs.user_id', 'ai_usage_logs.log_date');

        // 4. ADVANCED GROUPED LOGIC WITH JOIN
        $groupedQuery->leftJoinSub($distractionSubquery, 'distractions', function ($join) {
                $join->on('attendances.user_id', '=', 'distractions.user_id')
                     ->on('attendances.date', '=', 'distractions.log_date');
            })
            ->select(
                'attendances.user_id',
                'attendances.date',
                // Morning Shift (05:00 to 11:59)
                DB::raw("SUM(CASE WHEN HOUR(attendances.check_in) >= 5 AND HOUR(attendances.check_in) < 12 THEN attendances.work_time ELSE 0 END) as morning_seconds"),
                // Afternoon Shift (12:00 to 16:59)
                DB::raw("SUM(CASE WHEN HOUR(attendances.check_in) >= 12 AND HOUR(attendances.check_in) < 17 THEN attendances.work_time ELSE 0 END) as afternoon_seconds"),
                // Evening/Night Shift (17:00 to 04:59)
                DB::raw("SUM(CASE WHEN HOUR(attendances.check_in) >= 17 OR HOUR(attendances.check_in) < 5 THEN attendances.work_time ELSE 0 END) as night_seconds"),
                // Totals, Extracted Telemetry & Integrity
                DB::raw('SUM(attendances.work_time) as total_work_seconds'),
                DB::raw('MIN(attendances.check_in) as first_entry'),
                DB::raw('MAX(attendances.check_out) as last_exit'),
                DB::raw('COUNT(attendances.id) as total_sessions'),
                DB::raw('MAX(attendances.status) as current_integrity'),
                DB::raw('COALESCE(MAX(distractions.total_distraction_seconds), 0) as total_distraction_seconds')
            )
            ->groupBy('attendances.user_id', 'attendances.date')
            ->orderBy('attendances.date', 'desc')
            ->orderBy(DB::raw('MIN(attendances.check_in)'), 'desc');

        $attendances = $groupedQuery->paginate(20)->withQueryString();

        // 5. ACCURATE INTEL CALCULATIONS FOR STATS CARDS
        $totalSeconds = $statsQuery->sum('work_time');
        $calculatedHours = $totalSeconds / 3600;

        // Today's Real-time Active Signal Check (⭐ FIX: Isolated by company)
        $activeNow = Attendance::whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
            ->whereNull('check_out')
            ->whereDate('date', Carbon::today($timezone)->toDateString())
            ->distinct('user_id')
            ->count();

        $stats = [
            'total_hours'    => round($calculatedHours, 2),
            'total_sessions' => $statsQuery->count(), 
            'active_now'     => $activeNow,
            'filtered_count' => $attendances->total()
        ];

        // Fleet selection for dropdown filter (⭐ FIX: Isolated by company)
        $employees = User::where('company_id', $company_id)->where('role', 'employee')->get();

        return view('admin.attendance.index', compact('attendances', 'employees', 'stats'));
    }

    /**
     * Delete operational log record
     */
    public function destroy($id)
    {
        // ⭐ FIX: Add security check before delete
        $attendance = Attendance::findOrFail($id);
        
        if ($attendance->user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $attendance->delete();
        
        return back()->with('success', 'Operational log entry purged successfully.');
    }
}
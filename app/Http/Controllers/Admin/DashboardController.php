<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Task, Attendance, Screenshot, AiUsageLog, ShiftReport};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage};
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // MAIN ADMIN DASHBOARD
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $timezone = 'Asia/Karachi';
        $today    = Carbon::today($timezone);

        // ── All employees ─────────────────────────────────────────
        $employees = User::with('department')
            ->where('role', 'employee')
            ->get();

        $totalEmployees = $employees->count();

        // ── Online employees (have open attendance today, updated in last 5 min) ──
        //  FIX: Removed $timezone from now() so it matches default Laravel Database Timezone
        $onlineEmployeeIds = Attendance::where('date', $today->toDateString())
            ->whereNull('check_out')
            ->where('updated_at', '>=', now()->subMinutes(5)) 
            ->pluck('user_id')
            ->toArray();

        $onlineCount = count($onlineEmployeeIds);

        // ── Active Tasks (not completed) ──────────────────────────
        $activeTasks  = Task::whereNotIn('status', ['completed'])->count();
        $pendingTasks = Task::where('status', 'to_do')->count();

        // ── Today's screenshots (pulses) ──────────────────────────
        $todayPulses = Screenshot::whereDate('created_at', $today)->count();

        // ── Today's AI usage summary (top tools) ──────────────────
        $todayAiSummary = AiUsageLog::whereDate('log_date', $today)
            ->where('ai_tool', '!=', 'IDLE')
            ->selectRaw('ai_tool, COALESCE(SUM(duration_seconds), 0) as total_seconds, COUNT(DISTINCT user_id) as user_count')
            ->groupBy('ai_tool')
            ->orderByDesc('total_seconds')
            ->take(6)
            ->get();

        // ── Social media today ────────────────────────────────────
        $socialTools   = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT'];
        $socialRecord  = AiUsageLog::whereDate('log_date', $today)
            ->whereIn('ai_tool', $socialTools)
            ->selectRaw('COUNT(DISTINCT user_id) as users, COALESCE(SUM(duration_seconds), 0) as seconds')
            ->first();

        $socialToday = (object) [
            'users'   => $socialRecord->users ?? 0,
            'seconds' => $socialRecord->seconds ?? 0
        ];

        // ── Per-employee live status ───────────────────────────────
        $employeeStatus = [];
        foreach ($employees as $emp) {
            $isOnline = in_array($emp->id, $onlineEmployeeIds);

            $att = Attendance::where('user_id', $emp->id)
                ->where('date', $today->toDateString())
                ->latest('updated_at')
                ->first();

            $latestSnap = Screenshot::where('user_id', $emp->id)
                ->whereDate('created_at', $today)
                ->latest()
                ->first();

            $empAiToday = AiUsageLog::where('user_id', $emp->id)
                ->whereDate('log_date', $today)
                ->where('ai_tool', '!=', 'IDLE')
                ->pluck('ai_tool')
                ->toArray();

            $empSocial = AiUsageLog::where('user_id', $emp->id)
                ->whereDate('log_date', $today)
                ->whereIn('ai_tool', $socialTools)
                ->exists();

            $empActiveTasks = Task::where('user_id', $emp->id)
                ->whereNotIn('status', ['completed'])
                ->count();

            $employeeStatus[] = [
                'employee'       => $emp,
                'is_online'      => $isOnline,
                'work_seconds'   => $att ? $att->work_time : 0,
                'check_in'       => $att ? $att->check_in : null,
                'latest_snap'    => $latestSnap,
                'ai_tools'       => $empAiToday,
                'social_alert'   => $empSocial,
                'active_tasks'   => $empActiveTasks,
            ];
        }

        // Sort: online first, then offline
        usort($employeeStatus, fn($a, $b) => $b['is_online'] <=> $a['is_online']);

        // ── Latest 6 screenshots across all employees ──────────────
        $liveScreenshots = Screenshot::with('user')
            ->whereDate('created_at', $today)
            ->latest()
            ->take(6)
            ->get();

        // ── Weekly work hours chart (all employees aggregate) ──────
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = Carbon::now($timezone)->subDays($i);
            $secs  = Attendance::whereDate('date', $date->toDateString())->sum('work_time') ?: 0;
            $weeklyData[] = [
                'day'   => $date->format('D'),
                'hours' => round($secs / 3600, 1),
            ];
        }

        // ── Shift reports (last 7 days) ────────────────────────────
        $shiftSummary = ShiftReport::with('user')
            ->where('report_date', '>=', Carbon::now($timezone)->subDays(7)->toDateString())
            ->orderByDesc('report_date')
            ->take(10)
            ->get();

        // ── Tasks overview ─────────────────────────────────────────
        $tasksByStatus = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'onlineCount',
            'onlineEmployeeIds',
            'activeTasks',
            'pendingTasks',
            'todayPulses',
            'todayAiSummary',
            'socialToday',
            'employeeStatus',
            'liveScreenshots',
            'weeklyData',
            'shiftSummary',
            'tasksByStatus'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // LIVE SCREENSHOTS JSON
    // ─────────────────────────────────────────────────────────────
    public function liveScreenshots()
    {
        $timezone = 'Asia/Karachi';
        $today    = Carbon::today($timezone);

        $shots = Screenshot::with('user')
            ->whereDate('created_at', $today)
            ->latest()
            ->take(12)
            ->get()
            ->map(fn($s) => [
                'id'           => $s->id,
                'user_name'    => $s->user->name ?? 'Unknown',
                'image_url'    => asset('storage/' . $s->image_path),
                'window_title' => $s->window_title ?? 'No title',
                'is_admin'     => (bool) $s->triggered_by_admin,
                'captured_at'  => $s->created_at->diffForHumans(),
            ]);

        return response()->json($shots);
    }

    // ─────────────────────────────────────────────────────────────
    // LIVE EMPLOYEE STATUS JSON
    // ─────────────────────────────────────────────────────────────
    public function liveEmployeeStatus()
    {
        $timezone    = 'Asia/Karachi';
        $today       = Carbon::today($timezone);
        $socialTools = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT'];

        //  FIX: Removed $timezone from now() here as well
        $onlineEmployeeIds = Attendance::where('date', $today->toDateString())
            ->whereNull('check_out')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->pluck('user_id')
            ->toArray();

        $data = User::where('role', 'employee')
            ->get()
            ->map(function ($emp) use ($onlineEmployeeIds, $today, $socialTools) {
                $isOnline = in_array($emp->id, $onlineEmployeeIds);

                $att = Attendance::where('user_id', $emp->id)
                    ->where('date', $today->toDateString())
                    ->latest('updated_at')
                    ->first();

                $latestSnap = Screenshot::where('user_id', $emp->id)
                    ->whereDate('created_at', $today)
                    ->latest()
                    ->first();

                $empAiToday = AiUsageLog::where('user_id', $emp->id)
                    ->whereDate('log_date', $today)
                    ->where('ai_tool', '!=', 'IDLE')
                    ->pluck('ai_tool')
                    ->toArray();

                $empSocial = AiUsageLog::where('user_id', $emp->id)
                    ->whereDate('log_date', $today)
                    ->whereIn('ai_tool', $socialTools)
                    ->exists();

                return [
                    'id'           => $emp->id,
                    'is_online'    => $isOnline,
                    'work_seconds' => $att ? $att->work_time : 0,
                    'check_in'     => $att && $att->check_in ? $att->check_in : null,
                    'ai_tools'     => $empAiToday,
                    'social_alert' => $empSocial,
                    'latest_snap'  => $latestSnap ? [
                        'url'   => asset('storage/' . $latestSnap->image_path),
                        'title' => $latestSnap->window_title ?? 'No title',
                        'time'  => $latestSnap->created_at->diffForHumans(),
                    ] : null,
                ];
            });

        return response()->json($data);
    }

    // ─────────────────────────────────────────────────────────────
    // AI HEATMAP JSON
    // ─────────────────────────────────────────────────────────────
    public function aiHeatmap()
    {
        $timezone    = 'Asia/Karachi';
        $today       = Carbon::today($timezone)->toDateString();
        $socialTools = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT'];

        $data = User::where('role', 'employee')
            ->with(['aiUsageLogs' => function ($q) use ($today) {
                $q->whereDate('log_date', $today)->where('ai_tool', '!=', 'IDLE');
            }])
            ->get()
            ->map(function ($emp) use ($socialTools) {
                $aiTools     = $emp->aiUsageLogs->whereNotIn('ai_tool', $socialTools);
                $socialLogs  = $emp->aiUsageLogs->whereIn('ai_tool', $socialTools);
                return [
                    'id'             => $emp->id,
                    'name'           => $emp->name,
                    'ai_seconds'     => $aiTools->sum('duration_seconds'),
                    'social_seconds' => $socialLogs->sum('duration_seconds'),
                    'ai_tools'       => $aiTools->pluck('ai_tool')->unique()->values(),
                    'social_tools'   => $socialLogs->pluck('ai_tool')->unique()->values(),
                ];
            });

        return response()->json($data);
    }

    // ─────────────────────────────────────────────────────────────
    // EMPLOYEE AI ACTIVITY JSON
    // ─────────────────────────────────────────────────────────────
    public function employeeAiActivity($id)
    {
        $timezone = 'Asia/Karachi';
        $today    = Carbon::today($timezone)->toDateString();

        $logs = AiUsageLog::where('user_id', $id)
            ->whereDate('log_date', $today)
            ->where('ai_tool', '!=', 'IDLE')
            ->get()
            ->map(fn($l) => [
                'tool'    => $l->ai_tool,
                'minutes' => round($l->duration_seconds / 60),
                'title'   => $l->activity_title,
            ]);

        return response()->json($logs);
    }

    // ─────────────────────────────────────────────────────────────
    // SHIFT INTEL PAGE
    // ─────────────────────────────────────────────────────────────
    public function shiftIntel(Request $request)
    {
        $timezone = 'Asia/Karachi';
        $period   = $request->get('period', 'today');
        $today    = Carbon::today($timezone);

        $query = ShiftReport::with('user');

        switch ($period) {
            case 'weekly':
                $query->whereBetween('report_date', [
                    Carbon::now($timezone)->startOfWeek()->toDateString(),
                    Carbon::now($timezone)->endOfWeek()->toDateString(),
                ]);
                break;
            case 'monthly':
                $query->whereMonth('report_date', $today->month)
                      ->whereYear('report_date',  $today->year);
                break;
            case 'yearly':
                $query->whereYear('report_date', $today->year);
                break;
            default:
                $query->where('report_date', $today->toDateString());
        }

        $reports = $query->orderByDesc('report_date')->paginate(20);

        return view('admin.reports.shifts', compact('reports', 'period'));
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE SCREENSHOT
    // ─────────────────────────────────────────────────────────────
    public function destroyScreenshot($id)
    {
        $shot = Screenshot::findOrFail($id);

        if (Storage::disk('public')->exists($shot->image_path)) {
            Storage::disk('public')->delete($shot->image_path);
        }

        $shot->delete();

        return response()->json(['success' => true, 'message' => 'Pulse terminated.']);
    }
}
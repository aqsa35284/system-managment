<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Task, Attendance, Screenshot, AiUsageLog, ShiftReport};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // ⭐ Auth Facade Added
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // MAIN ADMIN DASHBOARD
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $timezone   = 'Asia/Karachi';
        $today      = Carbon::today($timezone);
        $company_id = Auth::user()->company_id; // ⭐ Current Company Locked

        // ── All employees (Isolated) ──────────────────────────────
        $employees = User::with('department')
            ->where('company_id', $company_id)
            ->where('role', 'employee')
            ->get();

        $totalEmployees = $employees->count();

        // ── Online Employees (Isolated) ───────────────────────────
        $onlineEmployeeIds = Attendance::whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
            ->where('date', $today->toDateString())
            ->whereNull('check_out')
            ->where('updated_at', '>=', now()->subMinutes(5)) 
            ->pluck('user_id')
            ->toArray();

        $onlineCount = count($onlineEmployeeIds);

        // ── Active Tasks (Isolated) ───────────────────────────────
        $activeTasks  = Task::where('company_id', $company_id)->whereNotIn('status', ['completed'])->count();
        $pendingTasks = Task::where('company_id', $company_id)->where('status', 'to_do')->count();

        // ── Today's screenshots (Isolated) ────────────────────────
        $todayPulses = Screenshot::whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })->whereDate('created_at', $today)->count();

        // ── Today's AI usage summary (Isolated) ───────────────────
        $todayAiSummary = AiUsageLog::whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
            ->whereDate('log_date', $today)
            ->where('ai_tool', '!=', 'IDLE')
            ->selectRaw('ai_tool, COALESCE(SUM(duration_seconds), 0) as total_seconds, COUNT(DISTINCT user_id) as user_count')
            ->groupBy('ai_tool')
            ->orderByDesc('total_seconds')
            ->take(6)
            ->get();

        $socialTools   = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT', 'CHATGPT', 'GEMINI', 'DEEPSEEK', 'CLAUDE'];
        
        $socialRecord  = AiUsageLog::whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
            ->whereDate('log_date', $today)
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
                ->where('company_id', $company_id)
                ->whereNotIn('status', ['completed'])
                ->count();

            $totalWorkSeconds = $att ? $att->work_time : 0;
            $distractionSeconds = AiUsageLog::where('user_id', $emp->id)
                ->whereDate('log_date', $today)
                ->where('ai_tool', '!=', 'IDLE')
                ->sum('duration_seconds');
                
            $pureWorkSeconds = max(0, $totalWorkSeconds - $distractionSeconds);

            $employeeStatus[] = [
                'employee'            => $emp,
                'is_online'           => $isOnline,
                'work_seconds'        => $totalWorkSeconds,
                'distraction_seconds' => $distractionSeconds,
                'pure_work_seconds'   => $pureWorkSeconds,
                'check_in'            => $att ? $att->check_in : null,
                'latest_snap'         => $latestSnap,
                'ai_tools'            => $empAiToday,
                'social_alert'        => $empSocial,
                'active_tasks'        => $empActiveTasks,
            ];
        }

        usort($employeeStatus, fn($a, $b) => $b['is_online'] <=> $a['is_online']);

        // ── Latest 6 screenshots across all employees (Isolated) ───
        $liveScreenshots = Screenshot::with('user')
            ->whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
            ->whereDate('created_at', $today)
            ->latest()
            ->take(6)
            ->get();

        // ── Weekly work hours chart (Isolated) ─────────────────────
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = Carbon::now($timezone)->subDays($i);
            $secs  = Attendance::whereHas('user', function($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })->whereDate('date', $date->toDateString())->sum('work_time') ?: 0;
                
            $weeklyData[] = [
                'day'   => $date->format('D'),
                'hours' => round($secs / 3600, 1),
            ];
        }

        // ── Shift reports (Isolated) ───────────────────────────────
        $shiftSummary = ShiftReport::with('user')
            ->whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
            ->where('report_date', '>=', Carbon::now($timezone)->subDays(7)->toDateString())
            ->orderByDesc('report_date')
            ->take(10)
            ->get();

        // ── Tasks overview (Isolated) ──────────────────────────────
        $tasksByStatus = Task::where('company_id', $company_id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalEmployees', 'onlineCount', 'onlineEmployeeIds', 'activeTasks',
            'pendingTasks', 'todayPulses', 'todayAiSummary', 'socialToday',
            'employeeStatus', 'liveScreenshots', 'weeklyData', 'shiftSummary', 'tasksByStatus'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // LIVE SCREENSHOTS JSON (Isolated)
    // ─────────────────────────────────────────────────────────────
    public function liveScreenshots()
    {
        $timezone   = 'Asia/Karachi';
        $today      = Carbon::today($timezone);
        $company_id = Auth::user()->company_id;

        $shots = Screenshot::with('user')
            ->whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
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
    // LIVE EMPLOYEE STATUS JSON (Isolated)
    // ─────────────────────────────────────────────────────────────
    public function liveEmployeeStatus()
    {
        $timezone    = 'Asia/Karachi';
        $today       = Carbon::today($timezone);
        $company_id  = Auth::user()->company_id;
        $socialTools = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT', 'CHATGPT', 'GEMINI', 'DEEPSEEK', 'CLAUDE'];

        $onlineEmployeeIds = Attendance::whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })
            ->where('date', $today->toDateString())
            ->whereNull('check_out')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->pluck('user_id')
            ->toArray();

        $data = User::where('company_id', $company_id)
            ->where('role', 'employee')
            ->get()
            ->map(function ($emp) use ($onlineEmployeeIds, $today, $socialTools) {
                $isOnline = in_array($emp->id, $onlineEmployeeIds);

                $att = Attendance::where('user_id', $emp->id)->where('date', $today->toDateString())->latest('updated_at')->first();
                $latestSnap = Screenshot::where('user_id', $emp->id)->whereDate('created_at', $today)->latest()->first();

                $empAiToday = AiUsageLog::where('user_id', $emp->id)->whereDate('log_date', $today)->where('ai_tool', '!=', 'IDLE')->pluck('ai_tool')->toArray();
                $empSocial = AiUsageLog::where('user_id', $emp->id)->whereDate('log_date', $today)->whereIn('ai_tool', $socialTools)->exists();

                $totalWorkSeconds = $att ? $att->work_time : 0;
                $distractionSeconds = AiUsageLog::where('user_id', $emp->id)->whereDate('log_date', $today)->where('ai_tool', '!=', 'IDLE')->sum('duration_seconds');
                $pureWorkSeconds = max(0, $totalWorkSeconds - $distractionSeconds);

                return [
                    'id'                  => $emp->id,
                    'is_online'           => $isOnline,
                    'work_seconds'        => $totalWorkSeconds,
                    'distraction_seconds' => $distractionSeconds,
                    'pure_work_seconds'   => $pureWorkSeconds,
                    'check_in'            => $att && $att->check_in ? $att->check_in : null,
                    'ai_tools'            => $empAiToday,
                    'social_alert'        => $empSocial,
                    'latest_snap'         => $latestSnap ? [
                        'url'   => asset('storage/' . $latestSnap->image_path),
                        'title' => $latestSnap->window_title ?? 'No title',
                        'time'  => $latestSnap->created_at->diffForHumans(),
                    ] : null,
                ];
            });

        return response()->json($data);
    }

    // ─────────────────────────────────────────────────────────────
    // AI HEATMAP JSON (Isolated)
    // ─────────────────────────────────────────────────────────────
    public function aiHeatmap()
    {
        $timezone    = 'Asia/Karachi';
        $today       = Carbon::today($timezone)->toDateString();
        $company_id  = Auth::user()->company_id;
        $socialTools = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT', 'CHATGPT', 'GEMINI', 'DEEPSEEK', 'CLAUDE'];

        $data = User::where('company_id', $company_id)
            ->where('role', 'employee')
            ->with(['aiUsageLogs' => function ($q) use ($today) {
                $q->whereDate('log_date', $today)->where('ai_tool', '!=', 'IDLE');
            }])
            ->get()
            ->map(function ($emp) use ($socialTools) {
                $aiTools    = $emp->aiUsageLogs->whereNotIn('ai_tool', $socialTools);
                $socialLogs = $emp->aiUsageLogs->whereIn('ai_tool', $socialTools);
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
    // EMPLOYEE AI ACTIVITY JSON (Isolated)
    // ─────────────────────────────────────────────────────────────
    public function employeeAiActivity($id)
    {
        $company_id = Auth::user()->company_id;
        
        $empExists = User::where('id', $id)->where('company_id', $company_id)->exists();
        if (!$empExists) return response()->json([], 403);

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
    // SHIFT INTEL PAGE (Isolated)
    // ─────────────────────────────────────────────────────────────
    public function shiftIntel(Request $request)
    {
        $timezone   = 'Asia/Karachi';
        $period     = $request->get('period', 'all');
        $operator   = $request->get('user_id', '');
        $today      = Carbon::today($timezone);
        $company_id = Auth::user()->company_id;

        $query = ShiftReport::with('user')->whereHas('user', function($q) use ($company_id) {
            $q->where('company_id', $company_id);
        });

        if (!empty($operator)) {
            $query->where('user_id', $operator);
        }

        switch ($period) {
            case 'weekly':
                $startOfWeek = Carbon::now($timezone)->startOfWeek()->toDateString();
                $endOfWeek   = Carbon::now($timezone)->endOfWeek()->toDateString();
                $query->whereBetween('report_date', [$startOfWeek, $endOfWeek]);
                break;
            case 'monthly':
                $query->whereMonth('report_date', $today->month)->whereYear('report_date', $today->year);
                break;
            case 'yearly':
                $query->whereYear('report_date', $today->year);
                break;
            case 'today':
                $query->where('report_date', $today->toDateString());
                break;
        }

        $kpiData = (clone $query)->get();
        
        $stats = [
            'total_processed' => $kpiData->count(),
            'morning_count'   => $kpiData->where('shift_name', 'Morning')->count(),
            'afternoon_count' => $kpiData->where('shift_name', 'Afternoon')->count(),
            'evening_count'   => $kpiData->where('shift_name', 'Evening')->count(),
            'night_count'     => $kpiData->where('shift_name', 'Night')->count()
        ];

        $reports = $query->orderByDesc('report_date')
                         ->orderByDesc('created_at')
                         ->paginate(20)
                         ->withQueryString();

        return view('admin.reports.shifts', compact('reports', 'period', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE SCREENSHOT (Isolated)
    // ─────────────────────────────────────────────────────────────
    public function destroyScreenshot($id)
    {
        $company_id = Auth::user()->company_id;
        
        $shot = Screenshot::whereHas('user', function($q) use ($company_id) {
            $q->where('company_id', $company_id);
        })->findOrFail($id);

        if (Storage::disk('public')->exists($shot->image_path)) {
            Storage::disk('public')->delete($shot->image_path);
        }

        $shot->delete();

        return response()->json(['success' => true, 'message' => 'Pulse terminated.']);
    }
}
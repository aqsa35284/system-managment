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
        $company    = Auth::user()->company; 
        $company_id = $company->id; // ⭐ Current Company Locked

        // 🟢 FIX 1: Removed AI Tools from here so they are dynamically detected as "AI"
        $socialTools   = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT'];

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

        // ── Today's screenshots (Isolated & Feature Gated) ────────
        $todayPulses = 0;
        if ($company->hasFeature('screenshot_monitoring')) {
            $todayPulses = Screenshot::whereHas('user', function($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })->whereDate('created_at', $today)->count();
        }

        // ── Today's AI usage summary (Isolated & Feature Gated) ───
        $todayAiSummary = collect();
        if ($company->hasFeature('ai_tool_tracking')) {
            $todayAiSummary = AiUsageLog::whereHas('user', function($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->whereDate('log_date', $today)
                ->where('ai_tool', '!=', 'IDLE')
                ->whereNotIn('ai_tool', $socialTools) // 🟢 Dynamic AI Detection
                ->selectRaw('ai_tool, COALESCE(SUM(duration_seconds), 0) as total_seconds, COUNT(DISTINCT user_id) as user_count')
                ->groupBy('ai_tool')
                ->orderByDesc('total_seconds')
                ->take(6)
                ->get();
        }
        
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

            $empAiToday = [];
            if ($company->hasFeature('ai_tool_tracking')) {
                $empAiToday = AiUsageLog::where('user_id', $emp->id)
                    ->whereDate('log_date', $today)
                    ->where('ai_tool', '!=', 'IDLE')
                    ->whereNotIn('ai_tool', $socialTools) // 🟢 Only AI Tools
                    ->pluck('ai_tool')
                    ->toArray();
            }

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
                // 🟢 FIX 2: Added Image evidence if AI is detected
                'ai_activity_image'   => (!empty($empAiToday) && $latestSnap) ? asset('storage/' . $latestSnap->image_path) : null,
            ];
        }

        usort($employeeStatus, fn($a, $b) => $b['is_online'] <=> $a['is_online']);

        // ── Latest 6 screenshots across all employees (Isolated & Feature Gated) ───
        $liveScreenshots = collect();
        if ($company->hasFeature('screenshot_monitoring')) {
            $liveScreenshots = Screenshot::with('user')
                ->whereHas('user', function($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->whereDate('created_at', $today)
                ->latest()
                ->take(6)
                ->get();
        }

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
    // LIVE SCREENSHOTS PAGE (FIXED: Supports PDF Export & Filters)
    // ─────────────────────────────────────────────────────────────
    public function liveScreenshots(Request $request)
    {
        $company = Auth::user()->company;
        
        // 🟢 FIX 3: Feature Gating
        if (!$company->hasFeature('screenshot_monitoring')) {
            return redirect()->route('admin.dashboard')->with('error', 'Feature Locked! Upgrade your plan to access Live Screenshots.');
        }

        $timezone   = 'Asia/Karachi';
        $company_id = $company->id;
        $period     = $request->get('period', 'today'); // Default aaj ka data

        $query = Screenshot::with('user')
            ->whereHas('user', function($q) use ($company_id) {
                $q->where('company_id', $company_id);
            });

        // Filter Logic
        if ($period == 'today') {
            $query->whereDate('created_at', Carbon::today($timezone));
        } elseif ($period == 'weekly') {
            $query->whereBetween('created_at', [
                Carbon::now($timezone)->startOfWeek(),
                Carbon::now($timezone)->endOfWeek()
            ]);
        } elseif ($period == 'monthly') {
            $query->whereMonth('created_at', Carbon::now($timezone)->month)
                  ->whereYear('created_at', Carbon::now($timezone)->year);
        }

        $shots = $query->latest()->paginate(12);

        return view('admin.screenshots.index', compact('shots'));
    }

    // ─────────────────────────────────────────────────────────────
    // LIVE EMPLOYEE STATUS JSON (Isolated)
    // ─────────────────────────────────────────────────────────────
    public function liveEmployeeStatus()
    {
        $timezone    = 'Asia/Karachi';
        $today       = Carbon::today($timezone);
        $company     = Auth::user()->company;
        $company_id  = $company->id;
        
        // 🟢 Separated AI vs Social
        $socialTools = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT'];

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
            ->map(function ($emp) use ($onlineEmployeeIds, $today, $socialTools, $company) {
                $isOnline = in_array($emp->id, $onlineEmployeeIds);

                $att = Attendance::where('user_id', $emp->id)->where('date', $today->toDateString())->latest('updated_at')->first();
                $latestSnap = Screenshot::where('user_id', $emp->id)->whereDate('created_at', $today)->latest()->first();

                $empAiToday = [];
                if ($company->hasFeature('ai_tool_tracking')) {
                    $empAiToday = AiUsageLog::where('user_id', $emp->id)->whereDate('log_date', $today)->where('ai_tool', '!=', 'IDLE')->whereNotIn('ai_tool', $socialTools)->pluck('ai_tool')->toArray();
                }
                
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
                    // 🟢 Image with AI tool integration here as well
                    'ai_activity_image'   => (!empty($empAiToday) && $latestSnap) ? asset('storage/' . $latestSnap->image_path) : null,
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
    // AI HEATMAP PAGE (FIXED: Now returns a view instead of JSON)
    // ─────────────────────────────────────────────────────────────
    public function aiHeatmap()
    {
        $company = Auth::user()->company;

        // 🟢 FIX 3: Feature Gating
        if (!$company->hasFeature('ai_tool_tracking')) {
            return redirect()->route('admin.dashboard')->with('error', 'Feature Locked! Upgrade your plan to access AI Radar.');
        }

        $timezone    = 'Asia/Karachi';
        $today       = Carbon::today($timezone)->toDateString();
        $company_id  = $company->id;
        $socialTools = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT'];

        $heatmapData = User::where('company_id', $company_id)
            ->where('role', 'employee')
            ->with(['aiUsageLogs' => function ($q) use ($today) {
                $q->whereDate('log_date', $today)->where('ai_tool', '!=', 'IDLE');
            }])
            ->get()
            ->map(function ($emp) use ($socialTools, $today) {
                $aiTools    = $emp->aiUsageLogs->whereNotIn('ai_tool', $socialTools);
                $socialLogs = $emp->aiUsageLogs->whereIn('ai_tool', $socialTools);
                
                // 🟢 Added Image Snapshot with AI
                $snapshot = Screenshot::where('user_id', $emp->id)->whereDate('created_at', $today)->latest()->first();

                return [
                    'id'             => $emp->id,
                    'name'           => $emp->name,
                    'ai_seconds'     => $aiTools->sum('duration_seconds'),
                    'social_seconds' => $socialLogs->sum('duration_seconds'),
                    'ai_tools'       => $aiTools->pluck('ai_tool')->unique()->values(),
                    'social_tools'   => $socialLogs->pluck('ai_tool')->unique()->values(),
                    'last_snapshot'  => $snapshot ? asset('storage/' . $snapshot->image_path) : null,
                ];
            });

        return view('admin.ai_heatmap.index', compact('heatmapData'));
    }

    // ─────────────────────────────────────────────────────────────
    // EMPLOYEE AI ACTIVITY JSON (Isolated)
    // ─────────────────────────────────────────────────────────────
    public function employeeAiActivity($id)
    {
        $company = Auth::user()->company;
        $company_id = $company->id;
        
        if (!$company->hasFeature('ai_tool_tracking')) {
            return response()->json(['error' => 'Feature Locked'], 403);
        }
        
        $empExists = User::where('id', $id)->where('company_id', $company_id)->exists();
        if (!$empExists) return response()->json([], 403);

        $timezone = 'Asia/Karachi';
        $today    = Carbon::today($timezone)->toDateString();

        $logs = AiUsageLog::where('user_id', $id)
            ->whereDate('log_date', $today)
            ->where('ai_tool', '!=', 'IDLE')
            ->get()
            ->map(function($l) use ($id, $today) {
                // 🟢 Map Image with log
                $snap = Screenshot::where('user_id', $id)->whereDate('created_at', $today)->latest()->first();
                return [
                    'tool'    => $l->ai_tool,
                    'minutes' => round($l->duration_seconds / 60),
                    'title'   => $l->activity_title,
                    'image'   => $snap ? asset('storage/' . $snap->image_path) : null,
                ];
            });

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
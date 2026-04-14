<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Department;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Screenshot; 
use App\Models\AiUsageLog; 
use App\Models\AdminFeedback; 
use App\Models\EmployeeFeedback; 
use App\Models\ShiftReport; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        // ⭐ 1. TIMEZONE & DATE SETUP
        $today = Carbon::today();
        $now = Carbon::now();

        // ⭐ 2. FLEET & NODE STATS
        $totalEmployees = User::where('role', 'employee')->count();
        $activeEmployees = User::where('role', 'employee')->where('status', 'active')->count();
        $totalDepartments = Schema::hasTable('departments') ? Department::count() : 0;

        // ⭐ 3. MISSION & PROJECT STATS
        $activeProjects = Schema::hasTable('projects') ? Project::where('status', 'active')->count() : 0;
        $pendingProjects = Schema::hasTable('projects') ? Project::where('status', 'pending')->count() : 0;
        $recentProjects = Schema::hasTable('projects') ? Project::latest()->take(5)->get() : collect();

        // ⭐ 4. OBJECTIVE (TASK) STATS
        $totalTasks = Schema::hasTable('tasks') ? Task::count() : 0;
        $completedTasks = Schema::hasTable('tasks') ? Task::where('status', 'completed')->count() : 0;
        $recentTasks = Schema::hasTable('tasks') ? Task::with(['user', 'project'])->latest()->take(10)->get() : collect();

        // 🚀 5. TASK PROGRESS HEATMAP LOGIC
        $heatmapDays = [];
        $heatmapCounts = [];
        if (Schema::hasTable('tasks')) {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $heatmapDays[] = $date->format('D'); 
                $count = Task::whereDate('updated_at', $date->toDateString())->where('status', 'completed')->count();
                $heatmapCounts[] = $count;
            }
        }

        // 🚀 6. LIVE TELEMETRY & PERFORMANCE
        $liveAttendance = collect();
        $employeePerformance = collect();
        if (Schema::hasTable('attendances')) {
            $liveAttendance = Attendance::with(['user.department'])
                                ->whereDate('date', $today)
                                ->whereNull('check_out') 
                                ->latest('updated_at')
                                ->get();
            
            $employeePerformance = $liveAttendance->map(function($att) {
                $latestSnap = Screenshot::where('user_id', $att->user_id)->whereDate('created_at', Carbon::today())->latest()->first();
                $totalSeconds = $att->work_time ?? 0; 
                $h = floor($totalSeconds / 3600);
                $m = floor(($totalSeconds % 3600) / 60);
                $isSignalActive = Carbon::parse($att->updated_at)->diffInMinutes(now()) < 5;

                return (object)[
                    'user_id'           => $att->user_id,
                    'name'              => $att->user->name ?? 'Unknown Operator',
                    'is_online'         => $isSignalActive,
                    'watch_time'        => "{$h}h {$m}m",
                    'last_screenshot'   => $latestSnap ? $latestSnap->image_path : null,
                    'last_window'       => $latestSnap ? $latestSnap->window_title : 'Scanning...',
                    'user'              => $att->user,
                ];
            });
        }

        // 🛡️ 7. SECURITY BLACKLIST MONITORING
        $blacklist = ['facebook', 'youtube', 'netflix', 'instagram', 'whatsapp', 'tiktok'];
        $securityAlerts = [];
        foreach ($employeePerformance as $perf) {
            if (isset($perf->last_window)) {
                $windowTitle = strtolower($perf->last_window);
                foreach ($blacklist as $site) {
                    if (str_contains($windowTitle, $site)) {
                        $securityAlerts[] = (object)[
                            'operator' => $perf->name,
                            'violation' => $perf->last_window,
                            'time' => now()->format('H:i'),
                            'user_id' => $perf->user_id
                        ];
                        break; 
                    }
                }
            }
        }

        // 🚀 8. AI COMPLIANCE STATS
        $globalAiStats = ['work' => 100, 'ai_tool' => 0];
        $aiToolBreakdown = collect();
        if (Schema::hasTable('ai_usage_logs')) {
            $aiSeconds = AiUsageLog::whereDate('log_date', $today)->sum('duration_seconds') ?? 0;
            $totalWorkSeconds = Attendance::whereDate('date', $today)->sum('work_time') ?? 0;
            $aiPercentage = $totalWorkSeconds > 0 ? min(round(($aiSeconds / $totalWorkSeconds) * 100, 1), 100) : 0;
            $globalAiStats = ['work' => 100 - $aiPercentage, 'ai_tool' => $aiPercentage];
            $aiToolBreakdown = AiUsageLog::whereDate('log_date', $today)
                                ->select('ai_tool', DB::raw('SUM(duration_seconds) as total_time'))
                                ->groupBy('ai_tool')->orderBy('total_time', 'desc')->get();
        }

        // ✨ 9. DAILY SUMMARIES (Fix: Employee Reports Fetching)
        // Employee jo summaries bhej raha hai stop session par, wo yahan fetch hongi
        $shiftReports = ShiftReport::with('user')
                            ->whereDate('report_date', $today)
                            ->latest()
                            ->get();

        $adminFeedbacks = AdminFeedback::with('employee')->latest()->take(10)->get();
        $employeeFeedbacks = EmployeeFeedback::with(['user', 'task'])->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalEmployees', 'activeEmployees', 'activeProjects', 'pendingProjects', 
            'totalTasks', 'completedTasks', 'totalDepartments', 'recentProjects', 
            'recentTasks', 'employeePerformance', 'globalAiStats', 'aiToolBreakdown',
            'adminFeedbacks', 'employeeFeedbacks', 'shiftReports', // ⭐ Added shiftReports
            'heatmapDays', 'heatmapCounts', 'securityAlerts'
        ));
    }

    public function shiftIntel()
    {
        $reports = ShiftReport::with('user')->latest()->paginate(15);
        return view('admin.reports.shifts', compact('reports'));
    }

    public function destroyScreenshot($id)
    {
        try {
            $screenshot = Screenshot::findOrFail($id);
            if ($screenshot->image_path) Storage::disk('public')->delete($screenshot->image_path);
            $screenshot->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
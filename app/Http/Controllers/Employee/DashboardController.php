<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\Screenshot;
use App\Models\AiUsageLog; 
use App\Models\Attendance; 
use App\Models\AdminFeedback;
use App\Models\ShiftReport; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = Carbon::today();

        // 🚀 Timer Fix: Aaj ka active session uthayein
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $today)
                                ->whereNull('check_out')
                                ->first();
        
        $totalSeconds = $attendance ? $attendance->work_time : 0;

        $tasks = Task::where('user_id', $user->id)
                     ->with('project')
                     ->latest()
                     ->get();
        
        $fleetMembers = User::where('role', 'employee')
                            ->where('id', '!=', $user->id)
                            ->get();

        $adminFeedbacks = AdminFeedback::where('employee_id', $user->id)
                                        ->latest()
                                        ->take(10)
                                        ->get();

        $stats = [
            'today'   => Screenshot::where('user_id', $user->id)->whereDate('created_at', $today)->count(),
            'weekly'  => Screenshot::where('user_id', $user->id)->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count(),
            'monthly' => Screenshot::where('user_id', $user->id)->whereMonth('created_at', $now->month)->count(),
        ];

        return view('employee.dashboard', compact('user', 'tasks', 'fleetMembers', 'totalSeconds', 'stats', 'adminFeedbacks'));
    }

    /**
     * ⭐ Timer Sync logic for real-time Admin Monitoring
     */
    public function syncTimer(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())
                                ->whereNull('check_out')
                                ->whereDate('date', Carbon::today())
                                ->first();

        if ($attendance) {
            $seconds = $request->seconds;
            $totalHours = $seconds / 3600;

            $attendance->update([
                'work_time' => $seconds,
                'total_hours' => round($totalHours, 2),
                'status' => 'present' // Admin side green signal
            ]);
            
            return response()->json(['success' => true, 'synced' => $seconds]);
        }
        return response()->json(['success' => false, 'message' => 'Active session not found']);
    }

    public function checkTrigger()
    {
        $user = Auth::user();
        if ($user->screenshot_trigger) {
            $user->update(['screenshot_trigger' => false]);
            return response()->json(['trigger' => true]);
        }
        return response()->json(['trigger' => false]);
    }

    public function uploadScreenshot(Request $request)
    {
        $request->validate(['image' => 'required']);
        $img = $request->image;
        $img = str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $img);
        $data = base64_decode($img);
        
        $fileName = time() . '.jpg';
        $path = 'pulses/' . Auth::id() . '/' . $fileName;
        Storage::disk('public')->put($path, $data);
        
        Screenshot::create([
            'user_id'      => Auth::id(),
            'image_path'   => $path,
            'window_title' => $request->window_title ?? 'Active Node',
        ]);
        
        return response()->json(['success' => true]);
    }

    public function reportAiUsage(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();
        
        $log = AiUsageLog::firstOrCreate(
            ['user_id' => $userId, 'ai_tool' => $request->ai_tool, 'log_date' => $today],
            ['duration_seconds' => 0]
        );
        
        $log->increment('duration_seconds', 10);
        $log->update(['activity_title' => $request->window_title]);
        
        return response()->json(['success' => true]);
    }

    /**
     * 🏁 STOP SESSION & GENERATE REPORT
     */
    public function stopSession(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereNull('check_out')
                                ->whereDate('date', $today)
                                ->first();

        if ($attendance) {
            // 🚀 AI Stats Calculation
            $aiSeconds = AiUsageLog::where('user_id', $user->id)
                                   ->whereDate('log_date', $today)
                                   ->sum('duration_seconds');
            
            // Safety: Division by zero protection
            $workTime = $attendance->work_time > 0 ? $attendance->work_time : 1;
            $aiPercent = min(round(($aiSeconds / $workTime) * 100, 2), 100);

            // 🚀 Pulse Count
            $pulseCount = Screenshot::where('user_id', $user->id)
                                    ->whereDate('created_at', $today)
                                    ->count();

            // 🚀 Shift Report Persistence
            ShiftReport::create([
                'user_id'       => $user->id,
                'shift_name'    => $this->getCurrentShiftName(),
                'total_seconds' => $attendance->work_time,
                'ai_percentage' => $aiPercent,
                'pulse_count'   => $pulseCount,
                'task_summary'  => $request->summary ?? 'Shift completed by operator.',
                'report_date'   => $today
            ]);

            // 🚀 Close Attendance Session
            $attendance->update([
                'check_out' => Carbon::now(),
                'status'    => 'offline'
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No active session.']);
    }

    private function getCurrentShiftName() {
        $hour = now()->hour;
        if ($hour >= 5 && $hour < 12) return "MORNING";
        if ($hour >= 12 && $hour < 17) return "AFTERNOON";
        return "EVENING/NIGHT";
    }

    public function forwardTask(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $request->validate(['receiver_id' => 'required|exists:users,id']);
        
        $task->update([
            'user_id' => $request->receiver_id, 
            'status' => 'pending'
        ]);
        
        return response()->json(['success' => true]);
    }
}
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
        $today = Carbon::today();

        // 🚀 Active Session Fetch: Timer ko sahi jagah se resume karne ke liye
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $today)
                                ->whereNull('check_out')
                                ->latest()
                                ->first();
        
        $totalSeconds = $attendance ? $attendance->work_time : 0;

        // Tasks Sync: Sirf wahi tasks jo is operator ko assigned hain
        $tasks = Task::where('user_id', $user->id)
                     ->with('project')
                     ->latest()
                     ->get();
        
        $fleetMembers = User::where('role', 'employee')
                            ->where('id', '!=', $user->id)
                            ->get();

        // Admin Feedbacks: Task details ke saath fetch karein
        $adminFeedbacks = AdminFeedback::where('employee_id', $user->id)
                                        ->with('task')
                                        ->latest()
                                        ->take(10)
                                        ->get();

        $stats = [
            'today'   => Screenshot::where('user_id', $user->id)->whereDate('created_at', $today)->count(),
            'weekly'  => Screenshot::where('user_id', $user->id)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'monthly' => Screenshot::where('user_id', $user->id)->whereMonth('created_at', now()->month)->count(),
        ];

        return view('employee.dashboard', compact('user', 'tasks', 'fleetMembers', 'totalSeconds', 'stats', 'adminFeedbacks'));
    }

    /**
     * ⭐ LIVE HEARTBEAT: Admin side par Name aur Seconds Live karne ke liye
     */
    public function syncTimer(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())
                                ->whereNull('check_out')
                                ->whereDate('date', Carbon::today())
                                ->first();

        if ($attendance) {
            $seconds = (int) $request->seconds;
            $attendance->update([
                'work_time' => $seconds,
                'total_hours' => round($seconds / 3600, 2),
                'status' => 'present',
                'updated_at' => now() // Admin pulse refresh: "Active Now" logic
            ]);
            
            return response()->json(['success' => true, 'synced' => $seconds]);
        }
        return response()->json(['success' => false]);
    }

    /**
     * 🏁 STOP SESSION: Daily Summary & Exit Signal Logic (FIXED)
     */
    public function stopSession(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // 1. Current Session dhoondein (Null check_out hona lazmi hai)
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereNull('check_out')
                                ->whereDate('date', $today)
                                ->latest()
                                ->first();

        if ($attendance) {
            // AI Usage Stats Calculation
            $aiSeconds = AiUsageLog::where('user_id', $user->id)
                                   ->whereDate('log_date', $today)
                                   ->sum('duration_seconds');
            
            $workTime = $attendance->work_time > 0 ? $attendance->work_time : 1;
            $aiPercent = min(round(($aiSeconds / $workTime) * 100, 2), 100);

            // Screenshot captures count
            $pulseCount = Screenshot::where('user_id', $user->id)
                                    ->whereDate('created_at', $today)
                                    ->count();

            // ⭐ 2. Shift Report Create (Daily Summary save ho rahi hai)
            ShiftReport::create([
                'user_id'       => $user->id,
                'shift_name'    => $this->getCurrentShiftName(),
                'total_seconds' => $attendance->work_time,
                'ai_percentage' => $aiPercent,
                'pulse_count'   => $pulseCount,
                'task_summary'  => $request->summary ?? 'Shift concluded by operator.', // Daily summary input
                'report_date'   => $today
            ]);

            // ⭐ 3. EXIT SIGNAL: Attendance close karna taake "Broadcasting" ruk jaye
            $attendance->update([
                'check_out' => now(), // Ab Admin Dashboard par Exit Time show hoga
                'status'    => 'offline'
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No active session found.']);
    }

    private function getCurrentShiftName() {
        $hour = now()->hour;
        if ($hour >= 5 && $hour < 12) return "MORNING CYCLE";
        if ($hour >= 12 && $hour < 17) return "AFTERNOON CYCLE";
        return "EVENING/NIGHT CYCLE";
    }

    /**
     * 🚀 FORWARD TASK: Real-time Ownership transfer
     */
    public function forwardTask(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $request->validate(['receiver_id' => 'required|exists:users,id']);
        
        $task->update([
            'user_id' => $request->receiver_id, 
            'status' => 'pending' // Naye operator ke liye status reset
        ]);
        
        return response()->json(['success' => true]);
    }

    // --- 📸 PULSE MONITORING & AI LOGS (Barkrar rakha gaya hai) ---

    public function uploadScreenshot(Request $request) {
        if(!$request->image) return response()->json(['success' => false]);
        
        $img = str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $request->image);
        $path = 'pulses/' . Auth::id() . '/' . time() . '.jpg';
        Storage::disk('public')->put($path, base64_decode($img));
        
        Screenshot::create([
            'user_id' => Auth::id(), 
            'image_path' => $path, 
            'window_title' => $request->window_title ?? 'Active Node'
        ]);
        return response()->json(['success' => true]);
    }

    public function reportAiUsage(Request $request) {
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

    public function checkTrigger() {
        $user = Auth::user();
        if ($user->screenshot_trigger) {
            $user->update(['screenshot_trigger' => false]);
            return response()->json(['trigger' => true]);
        }
        return response()->json(['trigger' => false]);
    }
}
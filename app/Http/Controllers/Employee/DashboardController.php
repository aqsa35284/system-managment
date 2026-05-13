<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\{User, Task, Attendance, Screenshot, AiUsageLog, AdminFeedback, ShiftReport, TaskForward};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, DB, Log};
use Carbon\Carbon;
use thiagoalessio\TesseractOCR\TesseractOCR;

class DashboardController extends Controller
{
    // ⭐ Global Timezone
    protected $timezone = 'Asia/Karachi';

    // ─────────────────────────────────────────────────────────────
    // MAIN DASHBOARD
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $user     = Auth::user();
        $today    = Carbon::today($this->timezone);

        // ── 1. Assigned tasks (active only) ──────────────────────
        $tasks = Task::with(['project'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['completed'])
            ->latest()
            ->get();

        // ── 2. Commander feedbacks ───────────────────────────────
        $adminFeedbacks = AdminFeedback::with('task')
            ->where('employee_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // ── 3. Fleet members (For specific dashboard widgets) ────
        $fleetMembers = User::where('role', 'employee')
            ->where('id', '!=', $user->id)
            ->select('id', 'name', 'designation')
            ->orderBy('name')
            ->get();

        // ⭐ 3.1 All Users (For Chat Sidebar & Forward Task Dropdown)
        $users = User::where('id', '!=', $user->id)->get();

        // ── 4. Today's session restore ───────────────────────────
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->latest()
            ->first();

        $totalSeconds = $todayAttendance ? $todayAttendance->work_time : 0;

        // ── 5. Weekly work hours ─────────────────────────────────
        $weeklyWorkHours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now($this->timezone)->subDays($i);
            $secs = Attendance::where('user_id', $user->id)
                ->whereDate('date', $date->toDateString())
                ->sum('work_time') ?: 0;
            $weeklyWorkHours[] = [
                'day'   => $date->format('D'),
                'date'  => $date->toDateString(),
                'hours' => round($secs / 3600, 1),
            ];
        }

        // ── 6. Recent logs ───────────────────────────────────────
        $attendanceLogs = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('check_in')
            ->take(7)
            ->get();

        return view('employee.dashboard', compact(
            'user', 'tasks', 'adminFeedbacks', 'fleetMembers', 'users',
            'totalSeconds', 'weeklyWorkHours', 'attendanceLogs'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // SYNC TIMER (Persistent Sync)
    // ─────────────────────────────────────────────────────────────
    public function syncTimer(Request $request)
    {
        $today    = Carbon::today($this->timezone);
        $now      = Carbon::now($this->timezone);

        $att = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->first();

        if ($att) {
            $att->update([
                'work_time'  => (int)($request->seconds ?? $att->work_time),
                'updated_at' => $now, // ⭐ Timezone Fix applied
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // CHECK TRIGGER (Admin manual pulse)
    // ─────────────────────────────────────────────────────────────
    public function checkTrigger()
    {
        $userId = Auth::id();
        $user   = DB::table('users')->where('id', $userId)->first();

        if ($user && $user->screenshot_trigger) {
            DB::table('users')->where('id', $userId)->update(['screenshot_trigger' => 0]);
            return response()->json(['trigger' => true]);
        }

        return response()->json(['trigger' => false]);
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ UPLOAD SCREENSHOT (SMART TITLE GENERATION)
    // ─────────────────────────────────────────────────────────────
    public function uploadScreenshot(Request $request)
    {
        $userId   = Auth::id();
        $now      = Carbon::now($this->timezone); // ⭐ Pakistani Time

        if (!$request->image) return response()->json(['success' => false]);

        // 1. Save Image to Storage
        $img    = str_replace(['data:image/jpeg;base64,', 'data:image/png;base64,', ' '], ['', '', '+'], $request->image);
        $folder = 'pulses/' . $userId . '/' . $now->format('Y/m/d');
        $path   = $folder . '/' . time() . '_' . rand(100, 999) . '.jpg';

        Storage::disk('public')->put($path, base64_decode($img));

        $windowTitle    = $request->window_title ?? 'Active Node';
        $isAdminTrigger = (bool)($request->admin_trigger ?? false);

        // ⭐ Decide Label: Admin Trigger ya Auto Scan
        $triggerLabel = $isAdminTrigger ? '[Admin Trigger]' : '[Auto Scan]';

        $screenshot = Screenshot::create([
            'user_id'            => $userId,
            'image_path'         => $path,
            'window_title'       => $triggerLabel . ' Processing...', 
            'triggered_by_admin' => $isAdminTrigger,
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);

        // 2. ⭐ ADVANCED OCR LOGIC & SECURITY ALERTS
        $detectedTool = null;
        
        if (str_contains(strtolower($windowTitle), 'screen') || str_contains(strtolower($windowTitle), 'monitor') || $windowTitle === 'Active Node') {
            try {
                $fullImagePath = storage_path('app/public/' . $path);
                
                $ocr = new TesseractOCR($fullImagePath);
                $ocr->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
                
                $extractedText = $ocr->run();
                $detectedTool = $this->detectToolFromText($extractedText);
                
            } catch (\Exception $e) {
                Log::error('OCR Failed: ' . $e->getMessage()); 
                $detectedTool = $this->detectToolFromText($windowTitle);
            }
        } else {
            $detectedTool = $this->detectToolFromText($windowTitle);
        }

        // 3. ⭐ Final Title Update & Database Logging
        if ($detectedTool) {
            $finalTitle = $triggerLabel . ' - ' . $detectedTool;
            $screenshot->update(['window_title' => $finalTitle]);
            
            // ⭐ HIGH-LEVEL: Auto-Trigger next screenshot if restricted tool is found
            if (in_array($detectedTool, ['FACEBOOK', 'YOUTUBE', 'NETFLIX', 'TIKTOK'])) {
                User::where('id', $userId)->update(['screenshot_trigger' => 1]);
                Log::warning("Security Alert: Employee {$userId} restricted tool detected: {$detectedTool}");
            }
            
            $this->logAiUsage($userId, $detectedTool, $finalTitle, $this->timezone, 30);
        } else {
             // Agar OCR ko kuch na milay
             $baseTitle = (str_contains(strtolower($windowTitle), 'screen') || $windowTitle === 'Active Node') ? 'Clean Screen' : $windowTitle;
             $screenshot->update(['window_title' => $triggerLabel . ' - ' . $baseTitle]);
        }

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // REPORT ACTIVITY (Stealth AI Scan)
    // ─────────────────────────────────────────────────────────────
    public function reportAiUsage(Request $request)
    {
        $userId      = Auth::id();
        $windowTitle = $request->window_title ?? '';

        $detectedTool = $this->detectToolFromText($windowTitle);

        if ($detectedTool) {
            $duration = (int)($request->duration ?? 30);
            $this->logAiUsage($userId, $detectedTool, $windowTitle, $this->timezone, $duration);
        }

        return response()->json(['ok' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // REPORT IDLE (Inactivity detected on frontend)
    // ─────────────────────────────────────────────────────────────
    public function reportIdle(Request $request)
    {
        $userId   = Auth::id();
        $today    = Carbon::today($this->timezone);
        $now      = Carbon::now($this->timezone);

        $att = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->first();

        if ($att) {
            $att->update([
                'work_time'  => (int)($request->seconds ?? $att->work_time),
                'updated_at' => $now, // ⭐ Timezone Fix applied
            ]);
        }

        $this->logIdleUsage($userId, $today, $this->timezone);

        return response()->json(['ok' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // STOP SESSION (Conclude Shift)
    // ─────────────────────────────────────────────────────────────
    public function stopSession(Request $request)
    {
        $userId   = Auth::id();
        $now      = Carbon::now($this->timezone);
        $today    = Carbon::today($this->timezone);

        $att = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->first();

        if ($att) {
            $workSeconds = (int)($request->seconds ?? $att->work_time);
            $att->update([
                'check_out'  => $now->format('H:i:s'),
                'work_time'  => $workSeconds,
                'updated_at' => $now, // ⭐ Timezone Fix applied
            ]);

            $hour = $now->hour;
            if      ($hour >= 5  && $hour < 12) $shiftName = 'Morning';
            elseif  ($hour >= 12 && $hour < 17) $shiftName = 'Afternoon';
            elseif  ($hour >= 17 && $hour < 21) $shiftName = 'Evening';
            else                                $shiftName = 'Night';

            $aiSeconds  = AiUsageLog::where('user_id', $userId)
                ->whereDate('log_date', $today)
                ->where('ai_tool', '!=', 'IDLE')
                ->sum('duration_seconds') ?: 0;
            $aiPercent  = $workSeconds > 0 ? min(round(($aiSeconds / $workSeconds) * 100, 1), 100) : 0;
            $pulseCount = Screenshot::where('user_id', $userId)->whereDate('created_at', $today)->count();

            ShiftReport::create([
                'user_id'       => $userId,
                'shift_name'    => $shiftName,
                'total_seconds' => $workSeconds,
                'ai_percentage' => $aiPercent,
                'pulse_count'   => $pulseCount,
                'task_summary'  => $request->summary ?? 'Shift concluded.',
                'report_date'   => $today->toDateString(),
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // TASK ACTIONS (⭐ BUG FIXED HERE)
    // ─────────────────────────────────────────────────────────────
    public function forwardTask(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        // ⭐ FIX: Updated to match frontend variable names
        $request->validate([
            'forwarded_to'   => 'required|exists:users,id',
            'forward_reason' => 'required|string'
        ]);

        DB::transaction(function () use ($task, $request) {
            TaskForward::create([
                'task_id'      => $task->id,
                'from_user_id' => Auth::id(),
                'to_user_id'   => $request->forwarded_to, // ⭐ FIXED
                'note'         => $request->forward_reason, // ⭐ FIXED
            ]);
            
            // ⭐ Re-assign task to the selected employee
            $task->update(['user_id' => $request->forwarded_to]); 
        });

        // ⭐ FIX: Return back to page instead of blank JSON
        return back()->with('success', 'Task forwarded successfully to your colleague!');
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $task->update([
            'status'                => $request->status ?? $task->status,
            'completion_percentage' => $request->completion_percentage ?? $task->completion_percentage,
        ]);
        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ PRIVATE HELPERS (PRIORITY DETECTION & TIMEZONES)
    // ─────────────────────────────────────────────────────────────

    private function detectToolFromText(string $title): ?string
    {
        // ⭐ PRIORITY 1: Social Media ko Pehle Check Karein
        $socialMediaMap = [
            'youtube'    => 'YOUTUBE',
            'facebook'   => 'FACEBOOK',
            'fb.com'     => 'FACEBOOK',
            'netflix'    => 'NETFLIX',
            'tiktok'     => 'TIKTOK',
            'instagram'  => 'INSTAGRAM',
            'whatsapp'   => 'WHATSAPP',
            'twitter'    => 'TWITTER',
            'x.com'      => 'TWITTER'
        ];

        // ⭐ PRIORITY 2: AI Tools ko Baad Mein Check Karein
        $aiToolMap = [
            'chatgpt'    => 'CHATGPT',
            'openai'     => 'CHATGPT',
            'claude'     => 'CLAUDE',
            'anthropic'  => 'CLAUDE',
            'deepseek'   => 'DEEPSEEK',
            'gemini'     => 'GEMINI',
            'bard'       => 'GEMINI',
            'copilot'    => 'COPILOT',
            'perplexity' => 'PERPLEXITY',
            'grok'       => 'GROK',
            'midjourney' => 'MIDJOURNEY'
        ];

        $lower = strtolower($title);

        // Pehle YouTube/Facebook Check Hoga
        foreach ($socialMediaMap as $keyword => $toolName) {
            if (str_contains($lower, $keyword)) return $toolName;
        }

        // Agar Social Media nahi mila, tab Gemini waghera Check Hoga
        foreach ($aiToolMap as $keyword => $toolName) {
            if (str_contains($lower, $keyword)) return $toolName;
        }

        return null;
    }

    private function logAiUsage(int $userId, string $tool, string $title, string $timezone, int $duration = 30): void
    {
        $today = Carbon::today($timezone)->toDateString();
        $now   = Carbon::now($timezone); // ⭐ FIXED: Ab PK Time use hoga

        $exists = DB::table('ai_usage_logs')
            ->where(['user_id' => $userId, 'ai_tool' => $tool, 'log_date' => $today])
            ->exists();

        if ($exists) {
            DB::table('ai_usage_logs')
                ->where(['user_id' => $userId, 'ai_tool' => $tool, 'log_date' => $today])
                ->update([
                    'activity_title'   => $title,
                    'duration_seconds' => DB::raw("duration_seconds + {$duration}"),
                    'updated_at'       => $now, // ⭐ FIXED
                ]);
        } else {
            DB::table('ai_usage_logs')->insert([
                'user_id'          => $userId,
                'ai_tool'          => $tool,
                'log_date'         => $today,
                'activity_title'   => $title,
                'duration_seconds' => $duration,
                'created_at'       => $now, // ⭐ FIXED
                'updated_at'       => $now, // ⭐ FIXED
            ]);
        }
    }

    private function logIdleUsage(int $userId, Carbon $today, string $timezone): void
    {
        $dateString = $today->toDateString();
        $now = Carbon::now($timezone); // ⭐ FIXED: Ab PK Time use hoga

        $exists = DB::table('ai_usage_logs')
            ->where(['user_id' => $userId, 'ai_tool' => 'IDLE', 'log_date' => $dateString])
            ->exists();

        if ($exists) {
            DB::table('ai_usage_logs')
                ->where(['user_id' => $userId, 'ai_tool' => 'IDLE', 'log_date' => $dateString])
                ->update([
                    'activity_title'   => 'Inactivity detected',
                    'duration_seconds' => DB::raw('duration_seconds + 1200'),
                    'updated_at'       => $now, // ⭐ FIXED
                ]);
        } else {
            DB::table('ai_usage_logs')->insert([
                'user_id'          => $userId,
                'ai_tool'          => 'IDLE',
                'log_date'         => $dateString,
                'activity_title'   => 'Inactivity detected',
                'duration_seconds' => 1200,
                'created_at'       => $now, // ⭐ FIXED
                'updated_at'       => $now, // ⭐ FIXED
            ]);
        }
    }
}
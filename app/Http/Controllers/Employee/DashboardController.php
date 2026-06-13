<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Screenshot;
use App\Models\AiUsageLog;
use App\Models\AdminFeedback;
use App\Models\ShiftReport;
use App\Models\TaskForward;
use App\Models\EmployeeFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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
        $user  = Auth::user();
        $today = Carbon::today($this->timezone);

        $tasks = Task::with(['project'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['completed'])
            ->latest()
            ->get();

        $adminFeedbacks = AdminFeedback::with('task')
            ->where('employee_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $fleetMembers = User::where('role', 'employee')
            ->where('id', '!=', $user->id)
            ->select('id', 'name', 'designation')
            ->orderBy('name')
            ->get();

        $users = User::where('id', '!=', $user->id)->get();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->latest()
            ->first();

        $totalSeconds = $todayAttendance
            ? $todayAttendance->work_time
            : 0;

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

        $attendanceLogs = Attendance::where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('check_in')
            ->take(7)
            ->get();

        return view('employee.dashboard', compact(
            'user',
            'tasks',
            'adminFeedbacks',
            'fleetMembers',
            'users',
            'totalSeconds',
            'weeklyWorkHours',
            'attendanceLogs'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ SYNC TIMER
    // ─────────────────────────────────────────────────────────────
    public function syncTimer(Request $request)
    {
        $today = Carbon::today($this->timezone);

        $att = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->first();

        if ($att) {
            $att->update([
                'work_time' => (int) ($request->seconds ?? $att->work_time),
            ]);
            $att->touch();
        }

        return response()->json(['ok' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // CHECK TRIGGER
    // ─────────────────────────────────────────────────────────────
    public function checkTrigger()
    {
        $userId = Auth::id();

        $user = DB::table('users')
            ->where('id', $userId)
            ->first();

        if ($user && $user->screenshot_trigger) {

            DB::table('users')
                ->where('id', $userId)
                ->update(['screenshot_trigger' => 0]);

            return response()->json(['trigger' => true]);
        }

        return response()->json(['trigger' => false]);
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ UPLOAD SCREENSHOT
    // ─────────────────────────────────────────────────────────────
    public function uploadScreenshot(Request $request)
    {
        $userId = Auth::id();
        $now    = Carbon::now($this->timezone);

        if (!$request->image) {
            return response()->json(['success' => false]);
        }

        $img = str_replace(
            ['data:image/jpeg;base64,', 'data:image/png;base64,', ' '],
            ['', '', '+'],
            $request->image
        );

        $folder = 'pulses/' . $userId . '/' . $now->format('Y/m/d');
        $path   = $folder . '/' . time() . '_' . rand(100, 999) . '.jpg';

        Storage::disk('public')->put($path, base64_decode($img));

        $windowTitle    = $request->window_title ?? 'Active Node';
        $isAdminTrigger = (bool) ($request->admin_trigger ?? false);
        $triggerLabel   = $isAdminTrigger ? '[Admin Trigger]' : '[Auto Scan]';

        $screenshot = Screenshot::create([
            'user_id'            => $userId,
            'image_path'         => $path,
            'window_title'       => $triggerLabel . ' Processing...',
            'triggered_by_admin' => $isAdminTrigger,
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);

        $detectedTool  = null;
        $extractedText = '';
        $fullImagePath = storage_path('app/public/' . $path);

        try {
            $ocr = new TesseractOCR($fullImagePath);

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $ocr->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
            } else {
                $ocr->executable('/usr/bin/tesseract');
            }

            $extractedText = $ocr->run();

        } catch (\Exception $e) {

            try {
                $base64 = base64_encode(file_get_contents($fullImagePath));

                $response = Http::asForm()->post('https://api.ocr.space/parse/image', [
                    'apikey'      => env('OCR_API_KEY', 'helloworld'), // using env if available
                    'base64Image' => 'data:image/jpeg;base64,' . $base64,
                    'scale'       => 'true'
                ]);

                $result        = $response->json();
                $extractedText = $result['ParsedResults'][0]['ParsedText'] ?? '';

            } catch (\Exception $apiErr) {
                Log::error('Cloud OCR Failed: ' . $apiErr->getMessage());
            }
        }

        $detectedTool = $this->detectToolFromText($extractedText . ' ' . $windowTitle);
        $shouldAlert  = false;

        if ($detectedTool) {

            $finalTitle = $triggerLabel . ' - ⚠️ DETECTED: ' . $detectedTool;

            $screenshot->update(['window_title' => $finalTitle]);

            $restricted = ['FACEBOOK', 'YOUTUBE', 'NETFLIX', 'TIKTOK', 'INSTAGRAM', 'WHATSAPP', 'SNAPCHAT'];
            $shouldAlert = true;

            if (in_array($detectedTool, $restricted)) {

                User::where('id', $userId)->update(['screenshot_trigger' => 1]);

                Log::warning("Restricted Tool Detected: {$detectedTool} by User {$userId}");

                event(new \App\Events\ScreenAlert(
                    Auth::user()->name,
                    $detectedTool
                ));
            }

            $this->logAiUsage($userId, $detectedTool, $finalTitle, $this->timezone, 30);

        } else {

            $lowerSearch = strtolower($windowTitle . ' ' . $extractedText);

            if (
                str_contains($lowerSearch, 'visual studio') ||
                str_contains($lowerSearch, 'vscode') ||
                str_contains($lowerSearch, 'code.exe')
            ) {
                $baseTitle = 'Clean Work (VS Code)';
            } else {
                $baseTitle = (
                    str_contains(strtolower($windowTitle), 'screen') ||
                    $windowTitle === 'Active Node'
                )
                    ? 'Clean Screen'
                    : $windowTitle;
            }

            $screenshot->update(['window_title' => $triggerLabel . ' - 🟢 ' . $baseTitle]);
        }

        return response()->json([
            'success' => true,
            'alert'   => $shouldAlert,
            'tool'    => $detectedTool
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // REPORT AI USAGE
    // ─────────────────────────────────────────────────────────────
    public function reportAiUsage(Request $request)
    {
        $userId       = Auth::id();
        $windowTitle  = $request->window_title ?? '';
        $detectedTool = $this->detectToolFromText($windowTitle);

        if ($detectedTool) {
            $duration = (int) ($request->duration ?? 30);
            $this->logAiUsage($userId, $detectedTool, $windowTitle, $this->timezone, $duration);
        }

        return response()->json(['ok' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ REPORT IDLE 
    // ─────────────────────────────────────────────────────────────
    public function reportIdle(Request $request)
    {
        $userId = Auth::id();
        $today  = Carbon::today($this->timezone);

        $att = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->whereNull('check_out')
            ->first();

        if ($att) {
            $att->update([
                'work_time'  => (int) ($request->seconds ?? $att->work_time),
                'updated_at' => Carbon::now($this->timezone)
            ]);
        }

        $this->logIdleUsage($userId, $today, $this->timezone);

        return response()->json(['ok' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ STOP SESSION (FIXED: UNLIMITED REPORTS PER DAY)
    // ─────────────────────────────────────────────────────────────
    public function stopSession(Request $request)
    {
        $userId = Auth::id();
        $now    = Carbon::now($this->timezone);
        $today  = Carbon::today($this->timezone);

        // Get the latest attendance of today, whether checked out or not
        $att = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->latest()
            ->first();

        $workSeconds = (int) ($request->seconds ?? ($att ? $att->work_time : 0));

        // Update attendance time
        if ($att && is_null($att->check_out)) {
            $att->update([
                'check_out'  => $now->format('H:i:s'),
                'work_time'  => $workSeconds,
                'updated_at' => $now
            ]);
        } elseif ($att) {
            $att->update([
                'work_time'  => max($att->work_time, $workSeconds),
                'updated_at' => $now
            ]);
        }

        // ⭐ ALWAYS CREATE SHIFT REPORT (No condition required)
        $hour = $now->hour;
        if ($hour >= 5 && $hour < 12) {
            $shiftName = 'Morning';
        } elseif ($hour >= 12 && $hour < 17) {
            $shiftName = 'Afternoon';
        } elseif ($hour >= 17 && $hour < 21) {
            $shiftName = 'Evening';
        } else {
            $shiftName = 'Night';
        }

        $aiSeconds = AiUsageLog::where('user_id', $userId)
            ->whereDate('log_date', $today)
            ->where('ai_tool', '!=', 'IDLE')
            ->sum('duration_seconds') ?: 0;

        $aiPercent = $workSeconds > 0
            ? min(round(($aiSeconds / $workSeconds) * 100, 1), 100)
            : 0;

        $pulseCount = Screenshot::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();

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

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // FORWARD TASK
    // ─────────────────────────────────────────────────────────────
    public function forwardTask(Request $request, $id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'forwarded_to'   => 'required|exists:users,id',
            'forward_reason' => 'required|string'
        ]);

        DB::transaction(function () use ($task, $request) {

            TaskForward::create([
                'task_id'      => $task->id,
                'from_user_id' => Auth::id(),
                'to_user_id'   => $request->forwarded_to,
                'note'         => $request->forward_reason,
            ]);

            $task->update(['user_id' => $request->forwarded_to]);
        });

        return back()->with('success', 'Task forwarded successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ UPDATE TASK STATUS 
    // ─────────────────────────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'update_note'           => 'required|string|max:1000',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'completion_stage'      => 'nullable|in:progress,halfway,completed',
            'blockers'              => 'nullable|string|max:500',
        ]);

        $task = Task::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $today = Carbon::today($this->timezone);

        $task->days_worked = (int) ($task->days_worked ?? 0) + 1;

        if ($task->due_date && $today->gt(Carbon::parse($task->due_date))) {
            $task->overdue_days = (int) $today->diffInDays(Carbon::parse($task->due_date));
        } else {
            $task->overdue_days = 0;
        }

        $task->completion_stage = $request->completion_stage ?? 'progress';

        if ($request->completion_stage === 'completed') {
            $task->status                = 'completed';
            $task->completion_percentage = 100;
        } else {
            $task->status = 'in_progress';
            $task->completion_percentage = $request->completion_percentage ?? $task->completion_percentage;
        }

        $task->save();

        EmployeeFeedback::create([
            'user_id'               => Auth::id(),
            'task_id'               => $task->id,
            'update_note'           => $request->update_note,
            'blockers'              => $request->blockers ?? null,
            'completion_percentage' => (int) $task->completion_percentage,
        ]);

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ DETECT TOOL
    // ─────────────────────────────────────────────────────────────
    private function detectToolFromText(string $title): ?string
    {
        $lower = strtolower($title);

        $aiToolMap = [
            'gemini.google' => 'GEMINI',
            'chatgpt.com'   => 'CHATGPT',
            'claude.ai'     => 'CLAUDE',
            'deepseek'      => 'DEEPSEEK',
            'gemini'        => 'GEMINI',
            'chatgpt'       => 'CHATGPT',
            'openai'        => 'CHATGPT',
            'copilot'       => 'COPILOT',
            'perplexity'    => 'PERPLEXITY',
            'grok'          => 'GROK',
            'midjourney'    => 'MIDJOURNEY',
        ];

        $socialMediaMap = [
            'facebook.com'  => 'FACEBOOK',
            'youtube.com'   => 'YOUTUBE',
            'instagram.com' => 'INSTAGRAM',
            'whatsapp'      => 'WHATSAPP',
            'tiktok.com'    => 'TIKTOK',
            'netflix.com'   => 'NETFLIX',
            'twitter.com'   => 'TWITTER',
            'x.com'         => 'TWITTER',
            'snapchat'      => 'SNAPCHAT',
            'facebook'      => 'FACEBOOK',
            'youtube'       => 'YOUTUBE',
            'tiktok'        => 'TIKTOK',
        ];

        foreach ($aiToolMap as $keyword => $toolName) {
            if (str_contains($lower, $keyword)) {
                return $toolName;
            }
        }

        foreach ($socialMediaMap as $keyword => $toolName) {
            if (str_contains($lower, $keyword)) {
                return $toolName;
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ LOG AI USAGE
    // ─────────────────────────────────────────────────────────────
    private function logAiUsage(
        int $userId,
        string $tool,
        string $title,
        string $timezone,
        int $duration = 30
    ): void {

        $today = Carbon::today($timezone)->toDateString();
        $now   = Carbon::now($timezone);

        $existing = DB::table('ai_usage_logs')
            ->where([
                'user_id'  => $userId,
                'ai_tool'  => $tool,
                'log_date' => $today
            ])
            ->first();

        if ($existing) {
            DB::table('ai_usage_logs')
                ->where('id', $existing->id)
                ->update([
                    'activity_title'   => $title,
                    'duration_seconds' => DB::raw("duration_seconds + {$duration}"),
                    'updated_at'       => $now,
                ]);
        } else {
            DB::table('ai_usage_logs')
                ->insert([
                    'user_id'          => $userId,
                    'ai_tool'          => $tool,
                    'log_date'         => $today,
                    'activity_title'   => $title,
                    'duration_seconds' => $duration,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // ⭐ LOG IDLE
    // ─────────────────────────────────────────────────────────────
    private function logIdleUsage(
        int $userId,
        Carbon $today,
        string $timezone
    ): void {

        $now        = Carbon::now($timezone);
        $dateString = $today->toDateString();

        $existing = DB::table('ai_usage_logs')
            ->where([
                'user_id'  => $userId,
                'ai_tool'  => 'IDLE',
                'log_date' => $dateString
            ])
            ->first();

        if ($existing) {
            DB::table('ai_usage_logs')
                ->where('id', $existing->id)
                ->update([
                    'activity_title'   => 'Inactivity detected',
                    'duration_seconds' => DB::raw('duration_seconds + 1200'),
                    'updated_at'       => $now,
                ]);
        } else {
            DB::table('ai_usage_logs')
                ->insert([
                    'user_id'          => $userId,
                    'ai_tool'          => 'IDLE',
                    'log_date'         => $dateString,
                    'activity_title'   => 'Inactivity detected',
                    'duration_seconds' => 1200,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Department, Screenshot, AiUsageLog, Attendance};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Storage, DB};
use Carbon\Carbon;

class EmployeeController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // LIST ALL EMPLOYEES
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $employees   = User::with('department')->where('role', 'employee')->latest()->get();
        $departments = Department::all();

        return view('admin.employees.index', compact('employees', 'departments'));
    }

    // ─────────────────────────────────────────────────────────────
    // CREATE FORM
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        $departments = Department::all();
        return view('admin.employees.create', compact('departments'));
    }

    // ─────────────────────────────────────────────────────────────
    // STORE NEW EMPLOYEE (⭐ FIXED: Added Missing Fields)
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // ⭐ Validation updated to include Username, Phone, Salary, Join Date
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'designation'   => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'role'          => 'required|in:admin,employee',
            'username'      => 'nullable|string|max:255|unique:users,username',
            'phone'         => 'nullable|string|max:20',
            'salary'        => 'required|numeric',
            'join_date'     => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $input             = $request->except(['password', 'profile_image']);
        $input['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_image')) {
            $input['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        User::create($input);

        return redirect()->route('admin.employees.index')
            ->with('success', 'New Member Registered to Fleet!');
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW SINGLE EMPLOYEE
    // Includes: AI stats, screenshots, pulse count, social media activity
    // period filter: today / weekly / monthly / yearly
    // ─────────────────────────────────────────────────────────────
    public function show(Request $request, $id)
    {
        $employee = User::with('department')->findOrFail($id);
        $timezone = 'Asia/Karachi';
        $period   = $request->get('period', 'today');

        // ── Date range based on period ────────────────────────────
        [$startDate, $endDate] = $this->getPeriodRange($period, $timezone);

        // ── AI Logs (period filter) ───────────────────────────────
        $aiLogs = AiUsageLog::where('user_id', $id)
            ->whereBetween('log_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->latest()
            ->get();

        // ── AI Tool Breakdown (grouped) ───────────────────────────
        $aiToolBreakdown = AiUsageLog::where('user_id', $id)
            ->whereBetween('log_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('ai_tool, SUM(duration_seconds) as total_seconds, COUNT(*) as sessions')
            ->groupBy('ai_tool')
            ->orderByDesc('total_seconds')
            ->get();

        // ── Social Media / Blacklist tool detection ───────────────
        // Detect from window titles which sites employee visited
        $socialMediaKeywords = [
            'youtube'    => ['youtube', 'yt.be'],
            'facebook'   => ['facebook', 'fb.com'],
            'instagram'  => ['instagram'],
            'tiktok'     => ['tiktok'],
            'whatsapp'   => ['whatsapp', 'web.whatsapp'],
            'twitter'    => ['twitter', 'x.com'],
            'snapchat'   => ['snapchat'],
            'netflix'    => ['netflix'],
            'linkedin'   => ['linkedin'],
        ];

        // Screenshots in period with window titles
        $periodScreenshots = Screenshot::where('user_id', $id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('window_title')
            ->get();

        // Build social media activity summary
        $socialMediaActivity = collect();
        foreach ($socialMediaKeywords as $platform => $keywords) {
            $matches = $periodScreenshots->filter(function ($snap) use ($keywords) {
                $title = strtolower($snap->window_title ?? '');
                foreach ($keywords as $kw) {
                    if (str_contains($title, $kw)) return true;
                }
                return false;
            });

            if ($matches->count() > 0) {
                $socialMediaActivity->push((object)[
                    'platform'    => strtoupper($platform),
                    'count'       => $matches->count(),
                    'last_seen'   => $matches->sortByDesc('created_at')->first()->created_at,
                    'sample_shot' => $matches->sortByDesc('created_at')->first(),
                ]);
            }
        }
        $socialMediaActivity = $socialMediaActivity->sortByDesc('count')->values();

        // ── Pulse count (period filter) ───────────────────────────
        $pulseQuery = Screenshot::where('user_id', $id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $todayPulseCount     = $pulseQuery->count();
        $adminTriggerCount   = (clone $pulseQuery)->where('triggered_by_admin', true)->count();
        $employeeRandomCount = (clone $pulseQuery)->where('triggered_by_admin', false)->count();

        // ── Work time (period) ────────────────────────────────────
        $workSeconds = Attendance::where('user_id', $id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('work_time') ?: 1;

        // ── AI percentage ─────────────────────────────────────────
        $aiSeconds    = $aiLogs->sum('duration_seconds') ?: 0;
        $aiPercentage = min(round(($aiSeconds / $workSeconds) * 100, 1), 100);

        // ── Online status ─────────────────────────────────────────
        $isOnline = Attendance::where('user_id', $id)
            ->whereDate('date', now($timezone))
            ->whereNull('check_out')
            ->where('updated_at', '>=', now($timezone)->subMinutes(5))
            ->exists();

        // ── Latest screenshot ─────────────────────────────────────
        $latestScreenshot = Screenshot::where('user_id', $id)
            ->latest()
            ->first();

        // ── Attendance records for period ─────────────────────────
        $attendanceRecords = Attendance::where('user_id', $id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('date')
            ->get();

        // ── Total work hours formatted ────────────────────────────
        $totalWorkH       = floor($workSeconds / 3600);
        $totalWorkM       = floor(($workSeconds % 3600) / 60);
        $totalWorkDisplay = "{$totalWorkH}h {$totalWorkM}m";

        return view('admin.employees.show', compact(
            'employee',
            'aiLogs',
            'aiToolBreakdown',
            'todayPulseCount',
            'adminTriggerCount',
            'employeeRandomCount',
            'aiPercentage',
            'aiSeconds',
            'workSeconds',
            'totalWorkDisplay',
            'isOnline',
            'latestScreenshot',
            'attendanceRecords',
            'period',
            'socialMediaActivity',
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // HELPER: Get Carbon start/end for a period
    // ─────────────────────────────────────────────────────────────
    private function getPeriodRange(string $period, string $timezone): array
    {
        $now = Carbon::now($timezone);

        return match ($period) {
            'weekly'  => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'yearly'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default   => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    // ─────────────────────────────────────────────────────────────
    // EDIT FORM
    // ─────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $employee    = User::findOrFail($id);
        $departments = Department::all();

        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE EMPLOYEE (⭐ FIXED: Added Missing Fields)
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        // ⭐ Validation updated to include Username, Phone, Salary, Join Date, Designation, Role
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $id,
            'designation'   => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'role'          => 'required|in:admin,employee',
            'username'      => 'nullable|string|max:255|unique:users,username,' . $id,
            'phone'         => 'nullable|string|max:20',
            'salary'        => 'required|numeric',
            'join_date'     => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $input = $request->except(['password', 'profile_image']);

        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            if ($employee->profile_image) {
                Storage::disk('public')->delete($employee->profile_image);
            }
            $input['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $employee->update($input);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Profile configuration updated.');
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE EMPLOYEE — Atomic transaction
    // ─────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $employee = User::findOrFail($id);

        DB::transaction(function () use ($id, $employee) {
            if (Storage::disk('public')->exists('pulses/' . $id)) {
                Storage::disk('public')->deleteDirectory('pulses/' . $id);
            }
            if ($employee->profile_image) {
                Storage::disk('public')->delete($employee->profile_image);
            }

            Screenshot::where('user_id', $id)->delete();
            AiUsageLog::where('user_id', $id)->delete();
            Attendance::where('user_id', $id)->delete();

            \App\Models\Task::where('user_id', $id)->update(['user_id' => null]);

            $employee->delete();
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Member and all associated telemetry purged.');
    }

    // ─────────────────────────────────────────────────────────────
    // VIEW SCREENSHOTS (PULSES) — Admin trigger vs Employee random
    // ─────────────────────────────────────────────────────────────
    public function viewPulses(Request $request, $id)
    {
        $employee = User::findOrFail($id);
        $timezone = 'Asia/Karachi';
        $query    = Screenshot::where('user_id', $id);

        // Time filter
        if ($request->filter === 'today') {
            $query->whereDate('created_at', Carbon::today($timezone));
        } elseif ($request->filter === 'weekly') {
            $query->whereBetween('created_at', [
                Carbon::now($timezone)->startOfWeek(),
                Carbon::now($timezone)->endOfWeek(),
            ]);
        } elseif ($request->filter === 'monthly') {
            $query->whereMonth('created_at', Carbon::now($timezone)->month)
                  ->whereYear('created_at',  Carbon::now($timezone)->year);
        } elseif ($request->filter === 'yearly') {
            $query->whereYear('created_at', Carbon::now($timezone)->year);
        }

        // Type filter: admin_triggered / employee_random
        if ($request->type === 'admin') {
            $query->where('triggered_by_admin', true);
        } elseif ($request->type === 'employee') {
            $query->where('triggered_by_admin', false);
        }

        $screenshots = $query->latest()->paginate(24)->withQueryString();

        // Counts for tab badges
        $baseQuery = Screenshot::where('user_id', $id);
        if ($request->filter === 'today') {
            $baseQuery->whereDate('created_at', Carbon::today($timezone));
        }
        $adminCount    = (clone $baseQuery)->where('triggered_by_admin', true)->count();
        $employeeCount = (clone $baseQuery)->where('triggered_by_admin', false)->count();

        return view('admin.employees.pulses', compact(
            'employee', 'screenshots', 'adminCount', 'employeeCount'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // MANUAL SCREENSHOT TRIGGER
    // ─────────────────────────────────────────────────────────────
    public function triggerScreenshot($id)
    {
        $exists = User::where('id', $id)->where('role', 'employee')->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        \DB::table('users')->where('id', $id)->update(['screenshot_trigger' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Surveillance signal broadcasted to node!',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // LATEST ADMIN-TRIGGERED SCREENSHOT
    // ─────────────────────────────────────────────────────────────
    public function latestTriggerShot($id)
    {
        $shot = Screenshot::where('user_id', $id)
            ->where('triggered_by_admin', true)
            ->latest()
            ->first();

        if (!$shot) {
            return response()->json(null);
        }

        return response()->json([
            'id'           => $shot->id,
            'image_url'    => asset('storage/' . $shot->image_path),
            'window_title' => $shot->window_title ?? 'Unknown',
            'captured_at'  => $shot->created_at->format('d M Y | H:i:s'),
            'triggered_by_admin' => (bool) $shot->triggered_by_admin,
        ]);
    }
}
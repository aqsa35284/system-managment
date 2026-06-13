<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Department, Screenshot, AiUsageLog, Attendance, ShiftReport, AdminFeedback, Task, EmployeeFeedback, Company, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Storage, DB, Auth}; 
use Carbon\Carbon;

class EmployeeController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // LIST ALL EMPLOYEES (Isolate by Company)
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $company_id = Auth::user()->company_id;
        
        $employees   = User::with('department')
                           ->where('company_id', $company_id)
                           ->where('role', 'employee')
                           ->latest()
                           ->get();
                           
        $departments = Department::where('company_id', $company_id)->get();

        return view('admin.employees.index', compact('employees', 'departments'));
    }

    // ─────────────────────────────────────────────────────────────
    // CREATE FORM (Quota Tracker Included)
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        $company = Company::find(Auth::user()->company_id);
        
        // FIX: Saare users (Admin + Employees) ko count karein
        $currentCount = User::where('company_id', $company->id)->count();
        $maxLimit     = $company->max_employees;

        $departments  = Department::where('company_id', $company->id)->get();
        
        return view('admin.employees.create', compact('departments', 'currentCount', 'maxLimit'));
    }

    // ─────────────────────────────────────────────────────────────
    // STORE NEW EMPLOYEE (Security Lock Included)
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $company = Company::find(Auth::user()->company_id);
        
        // FIX: Ensure Admins are also counted in the quota check
        $currentCount = User::where('company_id', $company->id)->count();

        // 🛡️ SECURITY LOCK: Check Limit
        if ($currentCount >= $company->max_employees) {
            return back()->with('error', 'Action Denied! Quota Exceeded. Please contact Super Admin to upgrade your plan.');
        }

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

        $input               = $request->except(['password', 'profile_image']);
        $input['password']   = Hash::make($request->password);
        $input['company_id'] = $company->id; 

        if ($request->hasFile('profile_image')) {
            $input['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $newUser = User::create($input);

        // 📝 AUDIT LOG: Bulletproof Try-Catch block to ensure redirection
        try {
            AuditLog::create([
                'company_id'     => Auth::user()->company_id,
                'user_id'        => Auth::id(),
                'super_admin_id' => $company->super_admin_id ?? Auth::id(),
                'action'         => 'Created User',
                'details'        => 'Added a new ' . $newUser->role . ': ' . $newUser->name,
                'url'            => request()->url(),
                'ip'             => request()->ip(),
                'user_agent'     => request()->userAgent() ?? 'Unknown',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to write audit log during employee creation: ' . $e->getMessage());
        }

        // ⭐ SMART REDIRECT
        if (Auth::user()->role === 'super_admin') {
            return redirect()->route('superadmin.dashboard')->with('success', 'New Member Registered to Fleet!');
        }

        return redirect()->route('admin.employees.index')
            ->with('success', 'New Member Registered to Fleet!');
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW SINGLE EMPLOYEE 
    // ─────────────────────────────────────────────────────────────
    public function show(Request $request, $id)
    {
        $employee = User::with('department')->where('company_id', Auth::user()->company_id)->findOrFail($id);
        $timezone = 'Asia/Karachi';
        $period   = $request->get('period', 'today');

        [$startDate, $endDate] = $this->getPeriodRange($period, $timezone);

        $socialTools = ['YOUTUBE', 'FACEBOOK', 'INSTAGRAM', 'TIKTOK', 'WHATSAPP', 'TWITTER', 'NETFLIX', 'SNAPCHAT'];

        $aiToolBreakdown = AiUsageLog::where('user_id', $id)
            ->whereBetween('log_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereNotIn('ai_tool', array_merge($socialTools, ['IDLE']))
            ->selectRaw('ai_tool, SUM(duration_seconds) as total_seconds, COUNT(*) as sessions')
            ->groupBy('ai_tool')
            ->orderByDesc('total_seconds')
            ->get();

        $socialMediaActivity = AiUsageLog::where('user_id', $id)
            ->whereBetween('log_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('ai_tool', $socialTools)
            ->selectRaw('ai_tool as platform, SUM(duration_seconds) as seconds, COUNT(*) as count')
            ->groupBy('ai_tool')
            ->orderByDesc('count')
            ->get();

        $pulseQuery = Screenshot::where('user_id', $id)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        $todayPulseCount     = $pulseQuery->count();
        $adminTriggerCount   = (clone $pulseQuery)->where('triggered_by_admin', true)->count();
        $employeeRandomCount = (clone $pulseQuery)->where('triggered_by_admin', false)->count();

        $workSeconds = Attendance::where('user_id', $id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('work_time') ?: 0;

        $aiSeconds    = $aiToolBreakdown->sum('total_seconds') ?: 0;
        $aiPercentage = $workSeconds > 0 ? min(round(($aiSeconds / $workSeconds) * 100, 1), 100) : 0;

        $isOnline = Attendance::where('user_id', $id)
            ->whereDate('date', now($timezone))
            ->whereNull('check_out')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->exists();

        $latestScreenshot = Screenshot::where('user_id', $id)->latest()->first();

        $attendanceRecords = Attendance::where('user_id', $id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('date')
            ->get();

        $shiftReports = ShiftReport::where('user_id', $id)
            ->whereBetween('report_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->latest('report_date')
            ->take(5)
            ->get();

        $adminFeedbacks = AdminFeedback::where('employee_id', $id)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->latest()
            ->take(4)
            ->get();

        $assignedTasks = Task::where('user_id', $id)->latest()->take(6)->get();

        $empLogs = EmployeeFeedback::where('user_id', $id)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->latest()
            ->take(5)
            ->get();

        $totalWorkH       = floor($workSeconds / 3600);
        $totalWorkM       = floor(($workSeconds % 3600) / 60);
        $totalWorkDisplay = "{$totalWorkH}h {$totalWorkM}m";

        return view('admin.employees.show', compact(
            'employee', 'aiToolBreakdown', 'todayPulseCount', 
            'adminTriggerCount', 'employeeRandomCount', 'aiPercentage', 
            'aiSeconds', 'workSeconds', 'totalWorkDisplay', 'isOnline', 
            'latestScreenshot', 'attendanceRecords', 'period', 'socialMediaActivity',
            'shiftReports', 'adminFeedbacks', 'assignedTasks', 'empLogs'
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
        $employee    = User::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $departments = Department::where('company_id', Auth::user()->company_id)->get();

        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE EMPLOYEE 
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $employee = User::where('company_id', Auth::user()->company_id)->findOrFail($id);

        // 🛡️ THE SHIELD: Immunity Check (Admin cannot edit Super Admin)
        if ($employee->role === 'super_admin') {
            Auth::logout();
            return redirect('/login')->with('error', 'CRITICAL VIOLATION: You attempted to modify a Master Account. Your session has been terminated.');
        }

        // 🔒 CREDENTIAL LOCKS FOR NORMAL ADMIN
        if (Auth::user()->role !== 'super_admin') {
            
            // 1. Email Change Lock
            if ($request->has('email') && trim($request->email) !== trim($employee->email)) {
                return back()->with('error', 'Action Denied: You cannot change emails. Contact Super Admin.');
            }

            // 2. ⭐ PASSWORD 3-TIME LIMIT LOCK (SMART FIX)
            if ($request->filled('password')) {
                $passwordChangeCount = AuditLog::where('company_id', Auth::user()->company_id)
                    ->where('action', 'Password Changed - Emp ID: ' . $employee->id)
                    ->count();

                if ($passwordChangeCount >= 3) {
                    return back()->with('error', 'Limit Reached! You have already changed this employee\'s password 3 times. Only Super Admin can change it now.');
                }
            }
        }

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

        $passwordChanged = false; // Tracker

        // Password change is now allowed for BOTH Admin and Super Admin
        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->password);
            $passwordChanged = true;
        }

        if ($request->hasFile('profile_image')) {
            if ($employee->profile_image) {
                Storage::disk('public')->delete($employee->profile_image);
            }
            $input['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $employee->update($input);

        // 📝 AUDIT LOGS
        try {
            // General Update Log
            AuditLog::create([
                'company_id'     => Auth::user()->company_id,
                'user_id'        => Auth::id(),
                'super_admin_id' => Company::find(Auth::user()->company_id)->super_admin_id ?? Auth::id(),
                'action'         => 'Updated User',
                'details'        => 'Modified profile of ' . $employee->name,
                'url'            => request()->url(),
                'ip'             => request()->ip(),
                'user_agent'     => request()->userAgent() ?? 'Unknown',
            ]);

            // ⭐ PASSWORD CHANGE TRACKING LOG (Safe Log without breaking query later)
            if ($passwordChanged) {
                AuditLog::create([
                    'company_id'     => Auth::user()->company_id,
                    'user_id'        => Auth::id(),
                    'super_admin_id' => Company::find(Auth::user()->company_id)->super_admin_id ?? Auth::id(),
                    'action'         => 'Password Changed - Emp ID: ' . $employee->id,
                    'details'        => 'Password updated securely',
                    'url'            => request()->url(),
                    'ip'             => request()->ip(),
                    'user_agent'     => request()->userAgent() ?? 'Unknown',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to write audit log during employee update: ' . $e->getMessage());
        }

        // ⭐ SMART REDIRECT FIX: Agar Super Admin ne edit kiya hai toh usay Super Admin Dashboard bhejo
        if (Auth::user()->role === 'super_admin') {
            return redirect()->route('superadmin.dashboard')
                ->with('success', 'Profile updated successfully!');
        }

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee profile updated successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE EMPLOYEE
    // ─────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $employee = User::where('company_id', Auth::user()->company_id)->findOrFail($id);

        // 🛡️ THE SHIELD: Immunity Check
        if ($employee->role === 'super_admin') {
            Auth::logout();
            return redirect('/login')->with('error', 'CRITICAL VIOLATION: You attempted to delete a Master Account. Your session has been terminated.');
        }

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
            Task::where('user_id', $id)->update(['user_id' => null]);

            // 📝 AUDIT LOG: Bulletproof Try-Catch block
            try {
                AuditLog::create([
                    'company_id'     => Auth::user()->company_id,
                    'user_id'        => Auth::id(),
                    'super_admin_id' => Company::find(Auth::user()->company_id)->super_admin_id ?? Auth::id(),
                    'action'         => 'Deleted User',
                    'details'        => 'Purged user: ' . $employee->name,
                    'url'            => request()->url(),
                    'ip'             => request()->ip(),
                    'user_agent'     => request()->userAgent() ?? 'Unknown',
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to write audit log during employee deletion: ' . $e->getMessage());
            }

            $employee->delete();
        });

        // ⭐ SMART REDIRECT FIX: Agar Super Admin ne delete kiya hai toh usay Super Admin Dashboard bhejo
        if (Auth::user()->role === 'super_admin') {
            return redirect()->route('superadmin.dashboard')
                ->with('success', 'Account completely purged from system.');
        }

        return redirect()->route('admin.employees.index')
            ->with('success', 'Member and all associated telemetry purged.');
    }

    // ─────────────────────────────────────────────────────────────
    // VIEW SCREENSHOTS, TRIGGER SCREENSHOT, LATEST SHOT
    // ─────────────────────────────────────────────────────────────
    public function viewPulses(Request $request, $id)
    {
        $employee = User::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $timezone = 'Asia/Karachi';
        $query    = Screenshot::where('user_id', $id);

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

        if ($request->type === 'admin') {
            $query->where('triggered_by_admin', true);
        } elseif ($request->type === 'employee') {
            $query->where('triggered_by_admin', false);
        }

        $screenshots = $query->latest()->paginate(24)->withQueryString();

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

    public function triggerScreenshot($id)
    {
        $exists = User::where('id', $id)->where('company_id', Auth::user()->company_id)->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        DB::table('users')->where('id', $id)->update(['screenshot_trigger' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Surveillance signal broadcasted to node!',
        ]);
    }

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
            'id'                 => $shot->id,
            'image_url'          => asset('storage/' . $shot->image_path),
            'window_title'       => $shot->window_title ?? 'Unknown',
            'captured_at'        => $shot->created_at->format('d M Y | H:i:s'),
            'triggered_by_admin' => (bool) $shot->triggered_by_admin,
        ]);
    }
}
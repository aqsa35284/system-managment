<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Screenshot; 
use App\Models\AiUsageLog; 
use App\Models\Attendance; // 👈 Add kiya
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    // 1. Saare Members ki List
    public function index()
    {
        $employees = User::with('department')->where('role', 'employee')->latest()->get();
        $departments = Department::all(); 
        return view('admin.employees.index', compact('employees', 'departments'));
    }

    // 2. Naya Member banane ka form
    public function create()
    {
        $departments = Department::all();
        return view('admin.employees.create', compact('departments'));
    }

    // 3. Data Database mein Save karna (Fixed Logic)
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'designation'   => 'required',
            'department_id' => 'required|exists:departments,id',
            'role'          => 'required|in:admin,employee', 
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Input data ko filter karein
        $input = $request->except(['password', 'profile_image']);
        $input['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_image')) {
            $input['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        User::create($input);

        return redirect()->route('admin.employees.index')->with('success', 'New Member Registered to Fleet!');
    }

    // 🛰️ VIEW PULSES (Screenshots Monitoring)
    public function viewPulses(Request $request, $id)
    {
        $employee = User::findOrFail($id);
        $query = Screenshot::where('user_id', $id);

        // 🕒 Advanced Filters
        if ($request->filter == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($request->filter == 'weekly') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($request->filter == 'monthly') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        $screenshots = $query->latest()->paginate(24)->withQueryString(); // 👈 withQueryString zaroori hai filters ke liye

        return view('admin.employees.pulses', compact('employee', 'screenshots'));
    }

    // 📸 Manual Screenshot Trigger (Safe Mode)
    public function triggerScreenshot($id)
    {
        $employee = User::where('id', $id)->where('role', 'employee')->firstOrFail();
        
        $employee->update(['screenshot_trigger' => true]);

        return response()->json([
            'success' => true, 
            'message' => 'Surveillance signal broadcasted to node!'
        ]);
    }

    // 4. Single Member Details (Analytics)
    public function show($id)
    {
        $employee = User::with('department')->findOrFail($id);
        
        // AI Usage Statistics
        $aiLogs = AiUsageLog::where('user_id', $id)
                            ->whereDate('log_date', Carbon::today())
                            ->latest()
                            ->get();
        
        $todayPulseCount = Screenshot::where('user_id', $id)
                                    ->whereDate('created_at', Carbon::today())
                                    ->count();

        return view('admin.employees.show', compact('employee', 'aiLogs', 'todayPulseCount'));
    }

    // 5. Edit Form
    public function edit($id)
    {
        $employee = User::findOrFail($id);
        $departments = Department::all();
        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    // 6. Profile Configuration Update
    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,'.$id,
            'department_id' => 'required|exists:departments,id',
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

        return redirect()->route('admin.employees.index')->with('success', 'Profile configuration updated.');
    }

    // 7. Full System Purge (Delete Member & Data)
    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        
        // Physical Screenshots Folder delete
        if (Storage::disk('public')->exists('pulses/' . $id)) {
            Storage::disk('public')->deleteDirectory('pulses/' . $id);
        }
        
        // Profile Image delete
        if ($employee->profile_image) {
            Storage::disk('public')->delete($employee->profile_image);
        }

        // Database cleanup (Atomic relation cleanup)
        Screenshot::where('user_id', $id)->delete();
        AiUsageLog::where('user_id', $id)->delete();
        Attendance::where('user_id', $id)->delete(); // 👈 Attendance bhi delete hogi

        $employee->delete();
        
        return redirect()->route('admin.employees.index')->with('success', 'Member and all associated telemetry purged.');
    }
}
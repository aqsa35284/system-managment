<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{Company, User, AuditLog};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Hash, Storage, Auth, DB};

class DashboardController extends Controller
{
    // Helper function for Audit Logging
    private function logAction($action, $details, $company_id = null)
    {
        AuditLog::create([
            'super_admin_id' => Auth::id(),
            'company_id'     => $company_id,
            'action'         => $action,
            'details'        => $details,
            'url'            => request()->url(),
            'ip'             => request()->ip(),
        ]);
    }

    // 1. Dashboard Stats
    public function index()
    {
        $stats = [
            'total_companies'   => Company::count(),
            'active_companies'  => Company::where('status', 'active')->count(),
            'expired_companies' => Company::where('subscription_ends_at', '<', now())->count(),
            'unpaid_invoices'   => Company::where('payment_status', 'unpaid')->count(),
        ];
        
        $companies = Company::withCount(['employees', 'admins'])->latest()->get();
        
        return view('superadmin.dashboard', compact('stats', 'companies'));
    }

    // 2. Create View
    public function create() { 
        return view('superadmin.company.create'); 
    }

    // 3. Store New Tenant (UPDATED WITH PLAN TYPE)
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:companies,email|unique:users,email',
            'max_employees'  => 'required|integer|min:1',
            'admin_password' => 'required|string|min:6',
            'plan_type'      => 'required|in:starter,basic,pro,advanced,private_label,enterprise', // Added validation
        ]);

        $company = Company::create([
            'name'                 => $request->name,
            'email'                => $request->email,
            'max_employees'        => $request->max_employees,
            'plan_type'            => $request->plan_type, // Saved the plan type
            'status'               => 'active',
            'payment_status'       => 'unpaid',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        User::create([
            'name'       => $request->name . ' Admin',
            'email'      => $request->email,
            'password'   => Hash::make($request->admin_password),
            'role'       => 'admin',
            'company_id' => $company->id,
            'status'     => 'active'
        ]);

        $this->logAction('Created Company', 'Initialized new tenant: ' . $company->name . ' on [' . strtoupper($request->plan_type) . '] plan', $company->id);

        return redirect()->route('superadmin.dashboard')->with('success', 'Tenant initialized!');
    }

    // 4. CRUD Operations
    public function show($id) { 
        return view('superadmin.company.show', ['company' => Company::with(['admins', 'employees'])->findOrFail($id)]); 
    }
    
    public function edit($id) { 
        return view('superadmin.company.edit', ['company' => Company::findOrFail($id)]); 
    }

    // ⭐ FIX: Added 'plan_type' in update method so Super Admin can change plans
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        
        // Validation with ignore current company email to prevent errors
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:companies,email,'.$company->id,
            'max_employees' => 'required|integer|min:1',
            'plan_type'     => 'required|in:starter,basic,pro,advanced,private_label,enterprise',
        ]);

        // plan_type array mein add kar diya hai
        $company->update($request->only(['name', 'email', 'phone', 'max_employees', 'plan_type']));
        
        $this->logAction('Updated Company', 'Modified details for: ' . $company->name, $company->id);
        return redirect()->route('superadmin.dashboard')->with('success', 'Tenant updated!');
    }

    // 5. Impersonation (God Mode)
    public function impersonate($companyId)
    {
        $admin = User::where('company_id', $companyId)->where('role', 'admin')->firstOrFail();
        
        session([
            'superadmin_id'      => Auth::id(),
            'audited_company_id' => $companyId
        ]);
        
        $this->logAction('Impersonated Admin', 'Started God Mode on company: ' . $companyId, $companyId);
        
        Auth::login($admin);
        
        return redirect()->route('admin.dashboard')->with('success', 'God Mode Activated. Shadow Audit is tracking.');
    }

    // 6. EXIT GOD MODE
    public function exitImpersonation()
    {
        if (session()->has('superadmin_id')) {
            $superId = session('superadmin_id');
            session()->forget(['superadmin_id', 'audited_company_id']);
            Auth::loginUsingId($superId);
        }
        return redirect()->route('superadmin.dashboard');
    }

    // 7. KICK / UNBAN
    public function toggleStatus($id)
    {
        $company = Company::findOrFail($id);
        $newStatus = $company->status === 'active' ? 'suspended' : 'active';
        
        $company->update(['status' => $newStatus]);
        User::where('company_id', $id)->update(['status' => $newStatus]);
        
        if ($newStatus === 'suspended') {
            DB::table('sessions')->whereIn('user_id', User::where('company_id', $id)->pluck('id'))->delete();
            $msg = 'Tenant Kicked: Access terminated.';
        } else {
            $msg = 'Tenant Unbanned: Access restored.';
        }
        
        $this->logAction('Status Change', 'Company ' . $company->name . ' is now ' . $newStatus, $id);
        
        return back()->with('success', $msg);
    }

    // 8. RENEW SUBSCRIPTION
    public function renewSubscription(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update([
            'subscription_ends_at' => now()->addDays(30),
            'payment_status'       => 'paid',
            'status'               => 'active'
        ]);

        User::where('company_id', $id)->update(['status' => 'active']);
        
        $this->logAction('Subscription Renewed', 'Extended 30 days for: ' . $company->name, $id);

        return back()->with('success', 'Subscription successfully renewed!');
    }

    // 9. Purge Tenant
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $this->logAction('Deleted Tenant', 'Purged company: ' . $company->name, $id);
        
        if ($company->logo) Storage::disk('public')->delete($company->logo);
        $company->delete();
        
        return redirect()->route('superadmin.dashboard')->with('success', 'Tenant permanently purged!');
    }
}
<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{Company, User, AuditLog, SubscriptionPlan, Invoice};
use App\Support\PlanFeatures;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Hash, Storage, Auth};

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

    // 1. Dashboard Stats (THE WATCHTOWER)
    public function index()
    {
        // Grand Totals for Master Control View
        $totalCompanies = Company::count();
        $totalPartners  = User::where('role', 'partner')->count();
        $totalAdmins    = User::where('role', 'admin')->count();
        $totalEmployees = User::where('role', 'employee')->count();
        
        $stats = [
            'total_companies'   => $totalCompanies,
            'active_companies'  => Company::where('status', 'active')->count(),
            'expired_companies' => Company::where('subscription_ends_at', '<', now())->count(),
            'unpaid_invoices'   => Company::where('payment_status', 'unpaid')->count(),
        ];
        
        // Fetch companies with partner info and counts
        $companies = Company::with('partner')->withCount([
            'users as admins_count' => function ($query) {
                $query->where('role', 'admin');
            }, 
            'users as employees_count' => function ($query) {
                $query->where('role', 'employee');
            }
        ])->latest()->paginate(10);
        
        return view('superadmin.dashboard', compact('stats', 'totalCompanies', 'totalPartners', 'totalAdmins', 'totalEmployees', 'companies'));
    }

    // 2. Create View
    public function create() 
    { 
        PlanFeatures::syncOfficialPlans();
        $plans = SubscriptionPlan::orderBy('price', 'asc')->get();
        return view('superadmin.company.create', compact('plans')); 
    }

    // 3. Store New Tenant (By Super Admin)
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:companies,email|unique:users,email',
            'max_employees'  => 'required|integer|min:5', 
            'admin_password' => 'required|string|min:6',
            'plan_type'      => 'required|exists:subscription_plans,slug',
        ]);

        $plan = SubscriptionPlan::where('slug', $request->plan_type)->firstOrFail();

        $basePrice = $plan->price;
        $extraUsers = max(0, $request->max_employees - 5);
        $extraUserCost = $extraUsers * $plan->extra_user_rate;
        $finalAmount = $basePrice + $extraUserCost;

        $company = Company::create([
            'partner_id'           => null, // Master Record: Super Admin deployed
            'name'                 => $request->name,
            'email'                => $request->email,
            'max_employees'        => $request->max_employees,
            'plan_type'            => $request->plan_type,
            // 🚀 AUTO-ASSIGN FEATURES BASED ON SELECTED PLAN
            'features'             => $plan->features ?? PlanFeatures::forPlan($plan->slug),
            'status'               => 'active',
            'payment_status'       => 'unpaid',
            'subscription_ends_at' => now()->addDays(30),
        ]);

        User::create([
            'name'       => $request->name . ' Admin',
            'email'      => $request->email,
            'password'   => Hash::make($request->admin_password),
            'role'       => 'admin',
            'company_id' => $company->id,
            'status'     => 'active'
        ]);

        $invoice = Invoice::create([
            'company_id'     => $company->id,
            'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'plan_name'      => $plan->name,
            'total_users'    => $request->max_employees,
            'total_amount'   => $finalAmount,
            'status'         => 'unpaid',
            'due_date'       => now()->addDays(7)
        ]);

        $this->logAction('Deployed Tenant', "Deployed {$company->name} on {$plan->name}. Invoice {$invoice->invoice_number} generated for PKR " . number_format($finalAmount), $company->id);

        return redirect()->route('superadmin.dashboard')->with('success', "Tenant deployed successfully! Automated Invoice ({$invoice->invoice_number}) generated for PKR " . number_format($finalAmount));
    }

    // 4. CRUD Operations
    public function show($id) { 
        return view('superadmin.company.show', ['company' => Company::with(['admins', 'employees'])->findOrFail($id)]); 
    }
    
    public function edit($id) { 
        $company = Company::findOrFail($id);
        $plans = SubscriptionPlan::orderBy('price', 'asc')->get(); 
        return view('superadmin.company.edit', compact('company', 'plans')); 
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:companies,email,'.$company->id,
            'max_employees' => 'required|integer|min:1',
            'plan_type'     => 'required|exists:subscription_plans,slug',
        ]);

        $plan = SubscriptionPlan::where('slug', $request->plan_type)->firstOrFail();

        $company->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'max_employees' => $request->max_employees,
            'plan_type'     => $request->plan_type,
            // 🚀 AUTO-SYNC FEATURES ON UPGRADE/DOWNGRADE
            'features'      => $plan->features ?? PlanFeatures::forPlan($plan->slug),
        ]);
        
        $this->logAction('Updated Company', 'Modified details for: ' . $company->name, $company->id);
        return redirect()->route('superadmin.dashboard')->with('success', 'Tenant updated! Features synced with plan.');
    }

    // 5. Impersonation (God Mode) - Super Admin to Tenant Admin
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

    public function exitImpersonation()
    {
        if (session()->has('superadmin_id')) {
            $superId = session('superadmin_id');
            session()->forget(['superadmin_id', 'audited_company_id']);
            Auth::loginUsingId($superId);
        }
        return redirect()->route('superadmin.dashboard');
    }

    // 6. Impersonation (God Mode) - Super Admin to Partner
    public function impersonatePartner($id)
    {
        $partner = User::where('id', $id)->where('role', 'partner')->firstOrFail();
        
        session(['superadmin_id' => Auth::id()]); 
        
        Auth::login($partner);
        
        return redirect()->route('partner.dashboard')->with('success', 'God Mode: Entered Partner Dashboard.');
    }

    public function exitPartnerImpersonation()
    {
        if (session()->has('superadmin_id')) {
            $superAdminId = session('superadmin_id');
            $superAdmin = User::findOrFail($superAdminId);
            
            session()->forget('superadmin_id');
            Auth::login($superAdmin);
            
            return redirect()->route('superadmin.partners.index')->with('success', 'Returned to Master Control.');
        }
        
        return redirect()->route('home');
    }

    public function toggleStatus($id)
    {
        $company = Company::findOrFail($id);
        $newStatus = $company->status === 'active' ? 'suspended' : 'active';
        
        $company->update(['status' => $newStatus]);
        User::where('company_id', $id)->update(['status' => $newStatus]);
        
        if ($newStatus === 'suspended') {
            $msg = 'Tenant Kicked: Access terminated.';
        } else {
            $msg = 'Tenant Unbanned: Access restored.';
        }
        
        $this->logAction('Status Change', 'Company ' . $company->name . ' is now ' . $newStatus, $id);
        
        return back()->with('success', $msg);
    }

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

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $this->logAction('Deleted Tenant', 'Purged company: ' . $company->name, $id);
        
        if ($company->logo) Storage::disk('public')->delete($company->logo);
        $company->delete();
        
        return redirect()->route('superadmin.dashboard')->with('success', 'Tenant permanently purged!');
    }
}
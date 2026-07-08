<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Support\PlanFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    private function partnerCompany(int $id): Company
    {
        return Company::where('partner_id', Auth::id())->findOrFail($id);
    }

    public function index()
    {
        $companies = Company::withCount([
            'users as admins_count' => fn ($q) => $q->where('role', 'admin'),
            'users as employees_count' => fn ($q) => $q->where('role', 'employee'),
        ])->where('partner_id', Auth::id())->latest()->get();

        $stats = [
            'total_companies'   => $companies->count(),
            'active_companies'  => $companies->where('status', 'active')->count(),
            'expired_companies' => $companies->filter(fn ($c) => $c->subscription_ends_at && $c->subscription_ends_at->isPast())->count(),
            'unpaid_invoices'   => Invoice::whereIn('company_id', $companies->pluck('id'))->where('status', 'unpaid')->count(),
        ];

        return view('partner.dashboard', compact('companies', 'stats'));
    }

    public function tenants()
    {
        $companies = Company::withCount([
            'users as admins_count' => fn ($q) => $q->where('role', 'admin'),
            'users as employees_count' => fn ($q) => $q->where('role', 'employee'),
        ])->where('partner_id', Auth::id())->latest()->paginate(10);

        return view('partner.tenants.index', compact('companies'));
    }

    public function createCompany()
    {
        PlanFeatures::syncOfficialPlans();
        $plans = SubscriptionPlan::orderBy('price', 'asc')->get();

        return view('superadmin.company.create', compact('plans'));
    }

    public function storeCompany(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:companies,email|unique:users,email',
            'max_employees'  => 'required|integer|min:5',
            'admin_password' => 'required|string|min:6',
            'plan_type'      => 'required|exists:subscription_plans,slug',
        ]);

        $plan = SubscriptionPlan::where('slug', $request->plan_type)->firstOrFail();

        $extraUsers = max(0, $request->max_employees - 5);
        $finalAmount = $plan->price + ($extraUsers * $plan->extra_user_rate);

        $company = Company::create([
            'partner_id'           => Auth::id(),
            'name'                 => $request->name,
            'email'                => $request->email,
            'phone'                => $request->phone,
            'max_employees'        => $request->max_employees,
            'plan_type'            => $plan->slug,
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
            'status'     => 'active',
        ]);

        Invoice::create([
            'company_id'     => $company->id,
            'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'plan_name'      => $plan->name,
            'total_users'    => $request->max_employees,
            'total_amount'   => $finalAmount,
            'status'         => 'unpaid',
            'due_date'       => now()->addDays(7),
        ]);

        return redirect()->route('partner.tenants.index')
            ->with('success', "Tenant deployed on {$plan->name} with full plan features. Invoice generated for PKR " . number_format($finalAmount));
    }

    public function show($id)
    {
        return view('superadmin.company.show', [
            'company' => $this->partnerCompany($id)->load(['admins', 'employees']),
        ]);
    }

    public function edit($id)
    {
        $company = $this->partnerCompany($id);
        PlanFeatures::syncOfficialPlans();
        $plans = SubscriptionPlan::orderBy('price', 'asc')->get();

        return view('superadmin.company.edit', compact('company', 'plans'));
    }

    public function update(Request $request, $id)
    {
        $company = $this->partnerCompany($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:companies,email,' . $company->id,
            'max_employees' => 'required|integer|min:5',
            'plan_type'     => 'required|exists:subscription_plans,slug',
        ]);

        $plan = SubscriptionPlan::where('slug', $request->plan_type)->firstOrFail();

        $company->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'max_employees' => $request->max_employees,
            'plan_type'     => $plan->slug,
            'features'      => $plan->features ?? PlanFeatures::forPlan($plan->slug),
        ]);

        return redirect()->route('partner.tenants.index')
            ->with('success', 'Tenant updated. Features synced with ' . $plan->name . ' plan.');
    }

    public function toggleStatus($id)
    {
        $company = $this->partnerCompany($id);
        $newStatus = $company->status === 'active' ? 'suspended' : 'active';

        $company->update(['status' => $newStatus]);
        User::where('company_id', $id)->update(['status' => $newStatus]);

        return back()->with('success', 'Tenant status updated to: ' . $newStatus);
    }

    public function impersonate($companyId)
    {
        $company = $this->partnerCompany($companyId);
        $admin = User::where('company_id', $company->id)->where('role', 'admin')->firstOrFail();

        session(['partner_id' => Auth::id(), 'audited_company_id' => $company->id]);
        Auth::login($admin);

        return redirect()->route('admin.dashboard')->with('success', 'God Mode Activated.');
    }

    public function exitImpersonation()
    {
        if (session()->has('partner_id')) {
            $partnerId = session('partner_id');
            session()->forget(['partner_id', 'audited_company_id']);
            Auth::loginUsingId($partnerId);
        }

        return redirect()->route('partner.dashboard');
    }

    public function renewSubscription(Request $request, $id)
    {
        $company = $this->partnerCompany($id);
        $company->update([
            'subscription_ends_at' => now()->addDays(30),
            'payment_status'       => 'paid',
            'status'               => 'active',
        ]);
        User::where('company_id', $id)->update(['status' => 'active']);

        return back()->with('success', 'Subscription renewed for 30 days.');
    }

    public function destroyCompany($id)
    {
        $company = $this->partnerCompany($id);
        $company->delete();

        return back()->with('success', 'Tenant removed from your portfolio.');
    }
}

<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Support\PlanFeatures;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        PlanFeatures::syncOfficialPlans();

        $plans = SubscriptionPlan::orderBy('price', 'asc')->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function edit($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $allFeatures = config('features.all', []);

        return view('superadmin.plans.edit', compact('plan', 'allFeatures'));
    }

    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $request->validate([
            'price'           => 'required|numeric|min:0',
            'extra_user_rate' => 'required|numeric|min:0',
            'retention_days'  => 'required|numeric|min:0',
            'features'        => 'nullable|array',
        ]);

        $newFeatures = array_values($request->features ?? []);

        $plan->update([
            'price'           => $request->price,
            'extra_user_rate' => $request->extra_user_rate,
            'retention_days'  => $request->retention_days,
            'features'        => $newFeatures,
        ]);

        Company::where('plan_type', $plan->slug)->get()->each(function (Company $company) use ($newFeatures) {
            $company->update(['features' => $newFeatures]);
        });

        $routePrefix = auth()->user()->role === 'partner' ? 'partner.' : 'superadmin.';

        return redirect()->route($routePrefix . 'plans.index')
            ->with('success', "{$plan->name} updated. All {$plan->name} tenants synced with the new feature matrix.");
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Support\PlanFeatures;
use Illuminate\Console\Command;

class SyncPlanFeatures extends Command
{
    protected $signature = 'eams:sync-plan-features';

    protected $description = 'Sync subscription_plans from PDF matrix and update all tenant feature flags';

    public function handle(): int
    {
        PlanFeatures::syncOfficialPlans();
        $this->info('Official plans synced from config/features.php');

        $updated = 0;
        Company::whereNotNull('plan_type')->each(function (Company $company) use (&$updated) {
            $plan = SubscriptionPlan::where('slug', $company->plan_type)->first();
            if (! $plan) {
                return;
            }
            $features = $plan->features ?? PlanFeatures::forPlan($company->plan_type);
            $company->update(['features' => $features]);
            $updated++;
        });

        $this->info("Updated features for {$updated} tenant(s).");

        return self::SUCCESS;
    }
}

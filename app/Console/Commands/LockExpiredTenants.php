<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LockExpiredTenants extends Command
{
    // Terminal mein run karne ke liye command ka naam
    protected $signature = 'tenant:lock-expired';

    // Command ki description
    protected $description = 'Locks companies whose subscription time has ended.';

    public function handle()
    {
        // Wo companies dhundhein jo active hain lekin unka time khatam ho chuka hai
        $expiredCompanies = Company::where('status', 'active')
            ->where('subscription_ends_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredCompanies as $company) {
            // 1. Company ko suspend karein aur unpaid mark karein
            $company->update([
                'status' => 'suspended',
                'payment_status' => 'unpaid'
            ]);

            // 2. Uske tamam users (Admin+Employees) ko suspend karein
            User::where('company_id', $company->id)->update(['status' => 'suspended']);

            // 3. Unhe foran system se bahar nikalne ke liye session destroy karein
            DB::table('sessions')->whereIn('user_id', User::where('company_id', $company->id)->pluck('id'))->delete();

            $count++;
        }

        $this->info("System check complete. {$count} expired tenants locked automatically.");
    }
}
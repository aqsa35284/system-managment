<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        // Super admin par ye check apply nahi hoga
        if (Auth::check() && Auth::user()->role !== 'super_admin') {
            
            $user = Auth::user();
            $company = Company::find($user->company_id);

            // Agar company record na mile
            if (!$company) {
                Auth::logout();
                return redirect('/login')->with('error', 'System Error: Company record missing.');
            }

            // Check 1: Kya Super Admin ne company suspend kar di hai?
            if ($company->status === 'suspended') {
                Auth::logout(); // Session destroy
                return redirect('/login')->with('error', 'Your company access has been terminated by the Super Admin.');
            }

            // Check 2: Kya SaaS subscription expire ho chuki hai?
            if ($company->subscription_ends_at && $company->subscription_ends_at < now()) {
                
                // Database mein status 'expired' update kar dein
                if ($company->status !== 'expired') {
                    $company->update(['status' => 'expired']);
                }

                // ⭐ ADVANCED SAAS LOGIC:
                // Employees aur Assistant Admins ko system se bahar nikal do
                if ($user->role === 'employee' || $user->role === 'assistant_admin') {
                    Auth::logout();
                    return redirect('/login')->with('error', 'Your company\'s subscription has expired. Please contact your administration.');
                }
                
                // Company Admin ko system mein rehnay do, lekin sirf Billing page par lock kar do
                if ($user->role === 'admin') {
                    // Agar wo pehle se billing page ya logout page par nahi hai, toh usay force redirect karo
                    if (!$request->routeIs('admin.billing*') && !$request->routeIs('logout')) {
                        // Note: Make sure aap web.php mein 'admin.billing' ka route zaroor banayen
                        return redirect()->route('admin.billing')->with('error', 'CRITICAL: Subscription Expired! Please renew your plan to restore workforce access.');
                    }
                }
            }
        }

        return $next($request);
    }
}
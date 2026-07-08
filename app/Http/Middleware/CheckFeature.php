<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFeature
{
    public function handle(Request $request, Closure $next, $feature)
    {
        $user = auth()->user();

        // Check if user belongs to a company and has the requested feature
        if ($user && $user->company && !$user->company->hasFeature($feature)) {
            
            // Agar ajax/api request hai toh JSON error return karo
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Feature locked. Upgrade plan.'], 403);
            }
            
            // Normal page load par dashboard wapas bhej do
            return redirect()->route('admin.dashboard')->with('error', 'Access Restricted! This feature requires a plan upgrade.');
        }

        return $next($request);
    }
}
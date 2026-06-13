<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check: Kya Super Admin ne God Mode (Impersonation) start kiya hua hai?
        if (session()->has('superadmin_id')) {
            return $next($request);
        }

        // 2. Check: Kya user ka role 'super_admin' hai?
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return $next($request);
        }

        // 3. Agar kuch bhi nahi, toh block kar do
        return redirect('/')->with('error', 'Access Denied! Only Master Control can access this area.');
    }
}
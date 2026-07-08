<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PartnerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Agar user logged in nahi hai toh Login page par bhejo
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 🟢 2. REAL-TIME KICK LOGIC (Status Check)
        // Agar admin ne suspend kar diya hai, toh dashboard se foran bahar nikal do
        if (Auth::user()->status === 'suspended' || Auth::user()->status === 'inactive') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('error', 'Your partner account has been suspended by Master Control.');
        }

        $role = Auth::user()->role;

        // 3. Agar partner hai aur active hai toh aage janay do
        if ($role === 'partner') {
            return $next($request);
        }

        // 4. Loop se bachne ke liye baqi roles ko unke dashboard par bhejein
        if ($role === 'admin' || $role === 'superadmin' || $role === 'super_admin') { 
            return redirect()->route('admin.dashboard'); 
        }

        if ($role === 'employee') {
            return redirect()->route('employee.dashboard'); 
        }

        // Agar koi un-defined role ho toh logout karwa dein aur login par bhejein
        Auth::logout();
        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
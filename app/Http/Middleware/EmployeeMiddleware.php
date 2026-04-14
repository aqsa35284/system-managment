<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check karein ke kya user logged in hai?
        // 2. Check karein ke kya uska role 'employee' hai?
        if (Auth::check() && Auth::user()->role === 'employee') {
            return $next($request);
        }

        // Agar employee nahi hai toh home page par redirect kar dein
        return redirect('/')->with('error', 'Access Denied. Only Node Operators (Employees) allowed.');
    }
}
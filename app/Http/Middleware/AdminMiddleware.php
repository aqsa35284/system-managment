<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check karein ke kya user logged in hai?
        // 2. Check karein ke kya uska role 'admin' hai?
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Agar admin nahi hai toh wapis bhej dein (Unauthorized Access)
        return redirect('/')->with('error', 'Command Center Access Denied. Admins Only.');
    }
}
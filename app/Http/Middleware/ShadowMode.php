<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShadowMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // TRUE GHOST MODE: Yeh tab activate hoga jab Super Admin kisi tenant mein enter hoga
        if (session()->has('superadmin_id')) {
            
            // UI ke liye flag set kar diya (taake aap views mein buttons hide kar sakein)
            session(['is_shadow_mode' => true]);

            // THE SPECTATOR FIREWALL: Agar Super Admin God Mode mein koi action (Save/Edit/Delete) karne ki koshish kare
            if ($request->method() !== 'GET' && !str_contains($request->path(), 'exit')) {
                
                // Agar API/Ajax request hai
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Shadow Mode Active: Modifications are blocked.'], 403);
                }
                
                // Normal Web Form submission
                return back()->with('error', 'GHOST MODE ALERT: You are in Spectator Mode. Write actions are blocked to leave zero footprints in the tenant database.');
            }
        } else {
            // Normal system behavior
            session(['is_shadow_mode' => false]);
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm() {
        // FIX: Agar user pehle se login hai, toh usay dashboard par bhej dein
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('employee.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('employee.dashboard');
        }

        return back()->withErrors(['email' => 'Authentication failed.']);
    }

    public function logout(Request $request) {
        Auth::logout();
        
        // Session clear karna zaroori hai
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // Logout ke baad login page par jayein
    }
}
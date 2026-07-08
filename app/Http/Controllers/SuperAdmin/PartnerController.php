<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // 🚀 YEH IMPORT ADD KIYA HAI IMPERSONATE KE LIYE

class PartnerController extends Controller
{
    // 1. Partners Ki List Dikhana
    public function index()
    {
        $partners = User::where('role', 'partner')
                        ->withCount('companies')
                        ->latest()
                        ->get();
                        
        return view('superadmin.partners.index', compact('partners'));
    }

    // 2. Naya Partner Add Karne ka Form
    public function create()
    {
        return view('superadmin.partners.create');
    }

    // 3. Naya Partner Add Karna
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'partner',
            'status'   => 'active', 
        ]);

        return redirect()->route('superadmin.partners.index')->with('success', 'New Partner Authorized Successfully!');
    }

    // 4. Partner Intel (Show Detail Page)
    public function show($id)
    {
        // Partner aur uski companies dono ko database se uthana
        $partner = User::with('companies')->findOrFail($id);

        // Security Check
        if ($partner->role !== 'partner') {
            return redirect()->route('superadmin.partners.index')->with('error', 'Invalid access. Not a partner.');
        }

        return view('superadmin.partners.show', compact('partner'));
    }

    // 5. Partner Edit Form
    public function edit($id)
    {
        $partner = User::findOrFail($id);
        
        if ($partner->role !== 'partner') {
            return redirect()->route('superadmin.partners.index')->with('error', 'Invalid access. Not a partner.');
        }

        return view('superadmin.partners.edit', compact('partner'));
    }

    // 6. Update Partner Details
    public function update(Request $request, $id)
    {
        $partner = User::findOrFail($id);
        
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,'.$id,
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|min:8'
        ]);

        $partner->name  = $request->name;
        $partner->email = $request->email;
        $partner->phone = $request->phone; 
        
        if($request->filled('password')) {
            $partner->password = Hash::make($request->password);
        }
        $partner->save();

        return redirect()->route('superadmin.partners.index')->with('success', 'Partner profile updated securely.');
    }

    // 7. Partner ko Delete karna (WITH DATA PROTECTION)
    public function destroy($id)
    {
        $partner = User::findOrFail($id);
        
        // 🛡️ DATA PROTECTION LOGIC: 
        // Partner delete karne par uski companies delete nahi hongi, 
        // wo seedha Super Admin (Master) ko transfer ho jayengi.
        Company::where('partner_id', $id)->update(['partner_id' => null]);
        
        $partner->delete();

        return back()->with('success', 'Partner removed. All their tenants have been transferred to Master Control.');
    }

    // 8. Partner ko Kick/Ban Karna
    public function toggle($id)
    {
        $partner = User::findOrFail($id);

        if ($partner->status === 'active') {
            $partner->status = 'suspended';
            $partner->save();
            
            // Partner ban hone par uski saari companies bhi suspend ho jayengi
            Company::where('partner_id', $id)->update(['status' => 'suspended']);
            return back()->with('success', 'Partner and their tenants have been SUSPENDED.');
        } else {
            $partner->status = 'active';
            $partner->save();
            
            // Partner unban hone par uski saari companies wapis active ho jayengi
            Company::where('partner_id', $id)->update(['status' => 'active']);
            return back()->with('success', 'Partner and their tenants have been RESTORED.');
        }
    }

    // ====================================================================
    // 🚀 NEW ADDITIONS: SUPER ADMIN GOD MODE (Impersonate Partner)
    // ====================================================================

    // 9. Impersonate Partner (Super Admin -> Partner)
    public function impersonate($id)
    {
        $partner = User::where('id', $id)->where('role', 'partner')->firstOrFail();
        
        // Super Admin ki ID session mein save karein
        session(['superadmin_id' => Auth::id()]); 
        
        // Partner ke roop mein login ho jayen
        Auth::login($partner);
        
        return redirect()->route('partner.dashboard')->with('success', 'God Mode: Entered Partner Workspace.');
    }

    // 10. Exit Impersonation (Wapas Super Admin par aane ke liye)
    public function exitImpersonation()
    {
        if (session()->has('superadmin_id')) {
            $superAdminId = session('superadmin_id');
            $superAdmin = User::findOrFail($superAdminId);
            
            // Session clear karein
            session()->forget('superadmin_id');
            
            // Wapas Master login karein
            Auth::login($superAdmin);
            
            return redirect()->route('superadmin.partners.index')->with('success', 'Returned to Master Control Panel.');
        }
        
        return redirect()->route('home');
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShadowAuditMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pehle se mojood Super Admin ki Impersonation Details
        $superAdminId = session('superadmin_id');
        $companyId = session('audited_company_id');

        // NAYA: Check karein ke kya Partner system use kar raha hai?
        $isPartner = Auth::check() && Auth::user()->role === 'partner';
        $isImpersonating = !empty($superAdminId) && !empty($companyId);

        // STRICT CHECK: Sirf actions log hongi (Ya toh Super Admin impersonate kar raha ho, YA Partner ho)
        if (($isPartner || $isImpersonating) && $request->method() !== 'GET') {
            
            // 1. Form Data Capture
            $payload = $request->except(['_token', '_method', 'password', 'password_confirmation', 'admin_password']);
            
            // ⭐ WATCHTOWER LOGIC: Agar Partner hai, toh uski pehchan chupke se payload mein daal do
            if ($isPartner) {
                $payload['Performed_By_Role'] = 'Partner';
                $payload['Performed_By_Name'] = Auth::user()->name;
                $payload['Performed_By_Email'] = Auth::user()->email;
            }
            
            // 2. ADVANCED INTEL: URL se ID pakar kar Name fetch karna (Aapki original brilliant logic)
            $routeParams = $request->route() ? $request->route()->parameters() : [];
            
            if (!empty($routeParams)) {
                // Agar URL mein koi ID pass hui hai (jaise Employee, Task, etc.)
                foreach ($routeParams as $key => $value) {
                    $id = is_object($value) ? $value->id : $value;
                    $payload['Target_ID'] = $id;

                    // Agar action Employee/User par hai
                    if ($key === 'employee' || $key === 'user' || $request->segment(2) === 'employees') {
                        $targetUser = User::find($id);
                        if ($targetUser) {
                            $payload['Target_Name'] = $targetUser->name;
                            $payload['Target_Email'] = $targetUser->email;
                        }
                    }
                }
            }

            $details = !empty($payload) ? json_encode($payload, JSON_PRETTY_PRINT) : null;
            $actionName = $this->determineActionName($request->method(), $request->path());

            // Actor ID Define karein: Agar Impersonating hai toh SuperAdmin, warna Partner ki apni ID
            $actorId = $isImpersonating ? $superAdminId : Auth::id();
            
            // Company ID: Agar partner company create/edit kar raha hai toh payload se uthayen warna session se
            $activeCompanyId = $isImpersonating ? $companyId : ($request->route('id') ?? $request->route('company') ?? null);

            \App\Models\AuditLog::create([
                'super_admin_id' => $actorId, // Yahan Master Admin ya Partner ki ID aayegi
                'company_id'     => $activeCompanyId,
                'action'         => $actionName,
                'details'        => $details, // Aapki Advanced Intel + Partner ki chori ki details
                'url'            => $request->fullUrl(),
                'ip'             => $request->ip(),
            ]);
        }

        return $next($request);
    }

    private function determineActionName(string $method, string $path): string
    {
        $target = 'Record';
        if (str_contains($path, 'employee')) $target = 'Employee';
        elseif (str_contains($path, 'task')) $target = 'Task';
        elseif (str_contains($path, 'project')) $target = 'Project';
        elseif (str_contains($path, 'company') || str_contains($path, 'partner')) $target = 'Tenant / Company'; // NAYA: Tenant tracking add ki

        return match ($method) {
            'POST' => 'Created ' . $target,
            'PUT', 'PATCH' => 'Updated ' . $target,
            'DELETE' => 'Deleted ' . $target,
            default => $method . ' Request',
        };
    }
}
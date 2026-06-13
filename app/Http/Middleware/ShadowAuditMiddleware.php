<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ShadowAuditMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $superAdminId = session('superadmin_id');
        $companyId = session('audited_company_id');

        // STRICT CHECK: Sirf actions log hongi
        if (!empty($superAdminId) && !empty($companyId) && $request->method() !== 'GET') {
            
            // 1. Form Data Capture
            $payload = $request->except(['_token', '_method', 'password', 'password_confirmation', 'admin_password']);
            
            // 2. ADVANCED INTEL: URL se ID pakar kar Name fetch karna
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

            \App\Models\AuditLog::create([
                'super_admin_id' => $superAdminId,
                'company_id'     => $companyId,
                'action'         => $actionName,
                'details'        => $details, // Ab yahan Name aur Email bhi aayega!
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

        return match ($method) {
            'POST' => 'Created ' . $target,
            'PUT', 'PATCH' => 'Updated ' . $target,
            'DELETE' => 'Deleted ' . $target,
            default => $method . ' Request',
        };
    }
}
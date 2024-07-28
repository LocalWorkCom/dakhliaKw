<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Rule;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next ,...$permissions): Response
    {
        $user = Auth::user();
        // dd($user);
        if (!$user) {
            return redirect('/login');
        }

        $rule_permission = Rule::find($user->rule_id);
        // dd($rule_permission );
        if (!$rule_permission) {
            abort(403, 'لايسمح لك بالدخول الى هذه الصفحة');
        }

        $permission_ids = explode(',', $rule_permission->permission_ids);

        // Fetch all permissions that the user has access to based on their role
        $allPermissions = Permission::whereIn('id', $permission_ids)->pluck('name')->toArray();
        // dd($allPermissions);

        // Check if the user has any of the required permissions
        foreach ($permissions as $permission) {
            // dd($permission);
            if (in_array($permission, $allPermissions)) {
                // dd("true");
                return $next($request);
            }
        }

        abort(403, 'لايسمح لك بالدخول الى هذه الصفحة');
    }
    
}

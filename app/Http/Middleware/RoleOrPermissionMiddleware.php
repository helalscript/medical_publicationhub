<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware as SpatieRoleOrPermissionMiddleware;

class RoleOrPermissionMiddleware extends SpatieRoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission, $guard = null)
    {
        $user = Auth::guard($guard)->user();

        if ($user && ($user->role ?? null) === 'admin') {
            return $next($request);
        }

        return parent::handle($request, $next, $roleOrPermission, $guard);
    }
}

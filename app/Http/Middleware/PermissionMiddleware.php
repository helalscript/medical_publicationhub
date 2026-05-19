<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Middleware\PermissionMiddleware as SpatiePermissionMiddleware;

class PermissionMiddleware extends SpatiePermissionMiddleware
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        $user = Auth::guard($guard)->user();

        if ($user && ($user->role ?? null) === 'admin') {
            return $next($request);
        }

        return parent::handle($request, $next, $permission, $guard);
    }
}

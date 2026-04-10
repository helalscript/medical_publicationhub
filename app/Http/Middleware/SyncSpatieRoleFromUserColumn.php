<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SyncSpatieRoleFromUserColumn
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user) {
            $roleName = $user->role;

            if (is_string($roleName) && $roleName !== '' && ! $user->hasRole($roleName)) {
                $user->syncRoles([$roleName]);
            }
        }

        return $next($request);
    }
}


<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleCheckMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role, ['admin', 'coordinator'], true)) {
            return abort(404, 'Unauthorized action.');
        }
        if (Auth::check()) {
            // Update 'last_seen' timestamp every time a user makes a request
            Auth::user()->update(['last_seen' => Carbon::now()]);
        }
        return $next($request);
    }
}

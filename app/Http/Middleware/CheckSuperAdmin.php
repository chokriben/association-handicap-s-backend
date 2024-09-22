<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user is a super admin
        if ($request->user() && $request->user()->role === 'super_admin') {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->is_approved) {
            if (!$request->routeIs('account.pending')) {
                return redirect()->route('account.pending');
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApproved
{
    /**
     * Si el usuario autenticado no está aprobado, redirige a /account/pending.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $user->is_approved) {
            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}

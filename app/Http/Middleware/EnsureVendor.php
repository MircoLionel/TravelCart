<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureVendor
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || (!$user->isVendor() && !$user->isAdmin())) {
            throw new AccessDeniedHttpException('Solo proveedores pueden acceder.');
        }

        return $next($request);
    }
}

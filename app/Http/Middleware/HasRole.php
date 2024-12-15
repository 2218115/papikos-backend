<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasRole
{
    public function handle($request, Closure $next, $roles)
    {
        if (!$request->user() || !$this->hasRole($request->user()->role, $roles)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }

    private function hasRole($userRole, $roles)
    {
        $allowedRoles = explode(',', $roles);
        return in_array($userRole, $allowedRoles);
    }
}

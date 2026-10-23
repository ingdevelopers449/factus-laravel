<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $roleCode = strtolower((string) $user->role?->code);
        $allowedRoles = array_map('strtolower', $roles);

        if (! in_array($roleCode, $allowedRoles, true)) {
            abort(403, 'No tienes permiso para acceder.');
        }

        return $next($request);
    }
}
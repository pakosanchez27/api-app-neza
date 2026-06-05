<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (filter_var(env('ADMIN_AUTH_BYPASS', false), FILTER_VALIDATE_BOOL)) {
            return $next($request);
        }

        $permissions = collect($request->session()->get('admin_permissions', []));

        abort_unless($permissions->contains($permission), 403);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminApp
{
    public function handle(Request $request, Closure $next): Response
    {
        $isAuthenticated = (bool) $request->session()->get('admin_auth');
        $token = $request->session()->get('admin_access_token');
        $user = $request->session()->get('admin_user', []);
        $isActive = (bool) ($user['activo'] ?? true);

        if (filter_var(env('ADMIN_AUTH_BYPASS', false), FILTER_VALIDATE_BOOL)) {
            if ($request->session()->get('admin_bypass_logged_out')) {
                return redirect()->route('admin.login');
            }

            return $next($request);
        }

        if (!$isAuthenticated || !$token || empty($user) || !$isActive) {
            return $this->invalidSessionRedirect($request);
        }

        return $next($request);
    }

    private function invalidSessionRedirect(Request $request): Response
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->withErrors([
                'email' => 'Tu sesion de administrador no es valida o ha expirado.',
            ]);
    }

}

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

        if (!$isAuthenticated || !$token || empty($user) || !$isActive) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'email' => 'Tu sesion de administrador no es valida o ha expirado.',
                ]);
        }

        return $next($request);
    }
}

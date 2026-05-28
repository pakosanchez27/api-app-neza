<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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

            if ($isAuthenticated && $token && ! empty($user) && $isActive) {
                $this->refreshAdminSession($request, $token);
            }

            return $next($request);
        }

        if (!$isAuthenticated || !$token || empty($user) || !$isActive) {
            return $this->invalidSessionRedirect($request);
        }

        if (! $this->refreshAdminSession($request, $token)) {
            return $this->invalidSessionRedirect($request);
        }

        return $next($request);
    }

    private function refreshAdminSession(Request $request, string $token): bool
    {
        $authApiUrl = (string) config('services.auth_api.url');
        $systemKey = (string) config('services.auth_api.system_key');

        try {
            $response = $this->authApiClient()
                ->withToken($token)
                ->get($authApiUrl . '/api/auth/me', [
                    'system_key' => $systemKey,
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        if ($response->failed()) {
            return false;
        }

        $payload = $response->json();
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];
        $user = is_array($data['user'] ?? null) ? $data['user'] : [];
        $system = is_array($data['system'] ?? null) ? $data['system'] : [];
        $roles = $this->normalizeCollection($data['roles'] ?? []);
        $permissions = $this->normalizeCollection($data['permissions'] ?? []);

        if (empty($user)) {
            return false;
        }

        $roleNames = $roles
            ->map(function ($role) {
                if (is_array($role)) {
                    return $role['name'] ?? $role['nombre'] ?? null;
                }

                return is_string($role) ? $role : null;
            })
            ->filter()
            ->values()
            ->all();

        $request->session()->put([
            'admin_user' => $user,
            'admin_system' => $system,
            'admin_roles' => $roles->all(),
            'admin_role_names' => $roleNames,
            'admin_permissions' => $permissions->all(),
        ]);

        return true;
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

    private function normalizeCollection(mixed $value): Collection
    {
        return collect(is_array($value) ? $value : []);
    }

    private function authApiClient()
    {
        $verify = (bool) config('services.auth_api.verify', true);
        $caBundle = config('services.auth_api.ca_bundle');

        if (is_string($caBundle) && $caBundle !== '') {
            $verify = $caBundle;
        }

        return Http::acceptJson()->withOptions([
            'verify' => $verify,
        ]);
    }
}

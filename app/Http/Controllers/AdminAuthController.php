<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommerceAdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AdminAuthController extends Controller
{
    public function create(Request $request)
    {
        if ($this->isAuthenticatedAdmin($request)) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function store(CommerceAdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        $authApiUrl = (string) config('services.auth_api.url');

        $loginResponse = Http::acceptJson()->post($authApiUrl.'/api/auth/login', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'system_key' => 'So3yVCHK7xtqEBWmhtWU7BEY',
        ]);



        if ($loginResponse->failed()) {
            return back()
                ->withErrors(['email' => 'Credenciales invalidas.'])
                ->withInput($request->only('email'));
        }

        $payload = $loginResponse->json();
        $data = $payload['data'] ?? [];
        $roles = $this->normalizeCollection($data['roles'] ?? []);
        $permissions = $this->normalizeCollection($data['permissions'] ?? []);
        $user = is_array($data['user'] ?? null) ? $data['user'] : [];
        $system = is_array($data['system'] ?? null) ? $data['system'] : [];
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

        $request->session()->regenerate();
        $request->session()->put([
            'admin_auth' => true,
            'admin_message' => $payload['message'] ?? null,
            'admin_access_token' => $data['access_token'] ?? null,
            'admin_token_type' => $data['token_type'] ?? 'Bearer',
            'admin_user' => $user,
            'admin_system' => $system,
            'admin_roles' => $roles->all(),
            'admin_role_names' => $roleNames,
            'admin_permissions' => $permissions->all(),
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $token = $request->session()->get('admin_access_token');
        $authApiUrl = (string) config('services.auth_api.url');

        if (!empty($token)) {
            $logoutResponse = Http::acceptJson()
                ->withToken($token)
                ->post($authApiUrl.'/api/auth/logout');

            // dd([
            //     'status' => $logoutResponse->status(),
            //     'ok' => $logoutResponse->ok(),
            //     'json' => $logoutResponse->json(),
            //     'body' => $logoutResponse->body(),
            // ]);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Sesion cerrada correctamente.');
    }

    private function isAuthenticatedAdmin(Request $request): bool
    {
        if (!$request->session()->get('admin_auth')) {
            return false;
        }

        $user = $request->session()->get('admin_user', []);
        $token = $request->session()->get('admin_access_token');
        $isActive = (bool) ($user['activo'] ?? true);

        return $isActive && !empty($user) && !empty($token);
    }

    private function normalizeCollection(mixed $value): Collection
    {
        return collect(is_array($value) ? $value : []);
    }
}

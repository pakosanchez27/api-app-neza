<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->session()->get('admin_user', []),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'email' => ['required', 'email', 'max:180'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato valido.',
        ]);

        $payload = Arr::only($validated, ['name', 'email']);

        try {
            $response = $this->authApiClient($request->session()->get('admin_access_token'))
                ->put($this->profileUrl(), $payload);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('sweet_alert', [
                    'icon' => 'error',
                    'title' => 'Error de conexion',
                    'text' => 'No fue posible comunicarse con el servicio de autenticacion.',
                ]);
        }

        if ($response->status() === 401) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Tu sesion expiro. Inicia sesion nuevamente.']);
        }

        if ($response->failed()) {
            $errors = $response->json('errors');
            $message = $response->json('message');

            return back()
                ->withInput()
                ->withErrors(is_array($errors) ? $errors : [
                    'profile' => is_string($message) && $message !== ''
                        ? $message
                        : 'No fue posible actualizar el perfil.',
                ])
                ->with('sweet_alert', [
                    'icon' => 'error',
                    'title' => 'No se pudo actualizar',
                    'text' => is_string($message) && $message !== ''
                        ? $message
                        : 'Revisa la informacion e intenta nuevamente.',
                ]);
        }

        $body = $response->json();
        $updatedUser = data_get($body, 'data.user')
            ?? data_get($body, 'user')
            ?? data_get($body, 'data');

        if (! is_array($updatedUser)) {
            $updatedUser = Arr::only($payload, ['name', 'email']);
        }

        $request->session()->put('admin_user', array_merge(
            $request->session()->get('admin_user', []),
            Arr::except($updatedUser, ['password', 'password_confirmation'])
        ));

        return redirect()->route('admin.perfil.edit')
            ->with('sweet_alert', [
                'icon' => 'success',
                'title' => 'Perfil actualizado',
                'text' => is_string(data_get($body, 'message'))
                    ? data_get($body, 'message')
                    : 'Tu informacion se actualizo correctamente.',
            ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Escribe tu contrasena actual.',
            'password.required' => 'Escribe una nueva contrasena.',
            'password.min' => 'La nueva contrasena debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmacion de la nueva contrasena no coincide.',
        ]);

        $session = session()->all();
        $token = $session['admin_access_token'] ?? null;
        $id_user = $session['admin_user']['id'] ?? null;

        if (! $token || ! $id_user) {
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'No fue posible identificar tu sesion. Inicia sesion nuevamente.']);
        }

        try {
            $response = $this->authApiClient((string) $token)
                ->put($this->passwordUrl($id_user), [
                    'current_password' => $validated['current_password'],
                    'password' => $validated['password'],
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('sweet_alert', [
                'icon' => 'error',
                'title' => 'Error de conexion',
                'text' => 'No fue posible comunicarse con el servicio de autenticacion.',
            ]);
        }

        if ($response->status() === 401) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Tu sesion expiro. Inicia sesion nuevamente.']);
        }

        if ($response->failed()) {
            $message = $this->passwordErrorMessage($response->status(), $response->json('message'));
            $errors = $response->json('errors');

            return back()
                ->withErrors(is_array($errors) ? $errors : ['password_update' => $message])
                ->with('sweet_alert', [
                    'icon' => 'error',
                    'title' => 'No se pudo cambiar la contrasena',
                    'text' => $message,
                ]);
        }

        return redirect()->route('admin.perfil.edit')
            ->with('sweet_alert', [
                'icon' => 'success',
                'title' => 'Contrasena actualizada',
                'text' => $response->json('message') ?: 'La contrasena se actualizo correctamente.',
            ]);
    }

    private function profileUrl(): string
    {
        return rtrim((string) config('services.auth_api.url'), '/')
            .'/'.ltrim((string) config('services.auth_api.profile_path'), '/');
    }

    private function passwordUrl(int|string $userId): string
    {
        $path = str_replace('{id}', (string) $userId, (string) config('services.auth_api.password_path'));

        return rtrim((string) config('services.auth_api.url'), '/').'/'.ltrim($path, '/');
    }

    private function passwordErrorMessage(int $status, mixed $apiMessage): string
    {
        if (is_string($apiMessage) && $apiMessage !== '') {
            return $apiMessage;
        }

        return match ($status) {
            403 => 'No tienes permiso para cambiar la contrasena de este usuario.',
            404 => 'El usuario indicado no existe.',
            422 => 'La contrasena actual no es correcta o los datos no son validos.',
            429 => 'Superaste el limite de intentos. Espera un minuto e intenta nuevamente.',
            default => 'No fue posible cambiar la contrasena.',
        };
    }

    private function authApiClient(?string $token): PendingRequest
    {
        $verify = (bool) config('services.auth_api.verify', true);
        $caBundle = config('services.auth_api.ca_bundle');

        if (is_string($caBundle) && $caBundle !== '') {
            $verify = $caBundle;
        }

        return Http::acceptJson()->withToken((string) $token)->withOptions([
            'verify' => $verify,
        ]);
    }
}

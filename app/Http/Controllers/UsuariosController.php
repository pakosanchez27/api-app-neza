<?php

namespace App\Http\Controllers;

use App\Mail\UserDeactivationMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class UsuariosController extends Controller
{
    private const CLIENT_ROLE_ID = 3;

    public function index(): View
    {
        $usuarios = User::query()
            ->where('id_rol', self::CLIENT_ROLE_ID)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function edit(User $user): View
    {
        $this->ensureClientUser($user);

        return view('admin.usuarios.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureClientUser($user);

        $incomingData = [
            'nombre_p' => $request->input('nombre_p'),
            'app_p' => $request->input('app_p'),
            'apm_p' => $request->input('apm_p'),
            'telefono' => $request->input('telefono'),
            'email' => $request->input('email'),
        ];

        $normalizedIncomingData = [
            'nombre_p' => $this->normalizeNullableString($incomingData['nombre_p']),
            'app_p' => $this->normalizeNullableString($incomingData['app_p']),
            'apm_p' => $this->normalizeNullableString($incomingData['apm_p']),
            'telefono' => $this->normalizeNullableString($incomingData['telefono']),
            'email' => $this->normalizeEmail($incomingData['email']),
        ];

        $currentData = [
            'nombre_p' => $this->normalizeNullableString($user->nombre_p),
            'app_p' => $this->normalizeNullableString($user->app_p),
            'apm_p' => $this->normalizeNullableString($user->apm_p),
            'telefono' => $this->normalizeNullableString($user->telefono),
            'email' => $this->normalizeEmail($user->email),
        ];

        $changedFields = [];

        foreach ($normalizedIncomingData as $field => $value) {
            if ($value !== $currentData[$field]) {
                $changedFields[$field] = $value;
            }
        }

        if ($changedFields === []) {
            return redirect()
                ->route('admin.usuarios')
                ->with('success', 'No hubo cambios para guardar.');
        }

        $rules = [];

        if (array_key_exists('nombre_p', $changedFields)) {
            $rules['nombre_p'] = ['required', 'string', 'max:60'];
        }

        if (array_key_exists('app_p', $changedFields)) {
            $rules['app_p'] = ['required', 'string', 'max:60'];
        }

        if (array_key_exists('apm_p', $changedFields)) {
            $rules['apm_p'] = ['nullable', 'string', 'max:60'];
        }

        if (array_key_exists('telefono', $changedFields)) {
            $rules['telefono'] = ['required', 'string', 'size:10', Rule::unique('users', 'telefono')->ignore($user->id)];
        }

        if (array_key_exists('email', $changedFields)) {
            $rules['email'] = ['required', 'string', 'email', 'max:80', Rule::unique('users', 'email')->ignore($user->id)];
        }

        $validatedData = $request->validate($rules, [
            'nombre_p.required' => 'El nombre es obligatorio.',
            'app_p.required' => 'El apellido paterno es obligatorio.',
            'telefono.required' => 'El numero es obligatorio.',
            'telefono.size' => 'El numero debe tener exactamente 10 digitos.',
            'telefono.unique' => 'Ese numero ya esta registrado.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato valido.',
            'email.unique' => 'Ese correo ya esta registrado.',
        ]);

        $updates = [];

        foreach (array_keys($changedFields) as $field) {
            $updates[$field] = match ($field) {
                'email' => $this->normalizeEmail($validatedData[$field] ?? null),
                default => $this->normalizeNullableString($validatedData[$field] ?? null),
            };
        }

        $nextNameParts = [
            'nombre_p' => $updates['nombre_p'] ?? $currentData['nombre_p'],
            'app_p' => $updates['app_p'] ?? $currentData['app_p'],
            'apm_p' => array_key_exists('apm_p', $updates) ? $updates['apm_p'] : $currentData['apm_p'],
        ];

        $updates['name'] = $this->buildFullName($nextNameParts);

        $user->fill($updates);
        $user->save();

        return redirect()
            ->route('admin.usuarios')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->ensureClientUser($user);

        if ((bool) $user->activo) {
            $validatedData = request()->validate([
                'motivo_desactivacion' => ['required', 'string', 'max:1000'],
            ], [
                'motivo_desactivacion.required' => 'Debes escribir el motivo de la desactivacion.',
                'motivo_desactivacion.max' => 'El motivo no puede tener mas de 1000 caracteres.',
            ]);

            if (! filled($user->email)) {
                return redirect()
                    ->route('admin.usuarios')
                    ->with('error', 'El usuario no tiene un correo registrado para notificar la desactivacion.');
            }

            try {
                Mail::to($user->email)->send(new UserDeactivationMail(
                    $user,
                    trim($validatedData['motivo_desactivacion'])
                ));
            } catch (Throwable $exception) {
                report($exception);

                return redirect()
                    ->route('admin.usuarios')
                    ->with('error', 'No fue posible enviar el correo de desactivacion. El usuario no fue desactivado.');
            }
        }

        $user->activo = ! (bool) $user->activo;
        $user->save();

        $message = $user->activo
            ? 'Usuario activado correctamente.'
            : 'Usuario desactivado correctamente.';

        return redirect()
            ->route('admin.usuarios')
            ->with('success', $message);
    }

    public function sendResetPassword(User $user): RedirectResponse
    {
        $this->ensureClientUser($user);

        if (! filled($user->email)) {
            return redirect()
                ->route('admin.usuarios')
                ->with('error', 'El usuario no tiene un correo registrado.');
        }

        $status = Password::broker('users')->sendResetLink([
            'email' => $user->email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            return redirect()
                ->route('admin.usuarios')
                ->with('error', 'No fue posible enviar el enlace de restablecimiento.');
        }

        return redirect()
            ->route('admin.usuarios')
            ->with('success', 'Se envio el enlace de restablecimiento al correo del usuario.');
    }

    private function ensureClientUser(User $user): void
    {
        abort_unless((int) $user->id_rol === self::CLIENT_ROLE_ID, 404);
    }

    private function buildFullName(array $validatedData): string
    {
        return trim(implode(' ', array_filter([
            $validatedData['nombre_p'] ?? null,
            $validatedData['app_p'] ?? null,
            $validatedData['apm_p'] ?? null,
        ])));
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private function normalizeEmail(mixed $value): ?string
    {
        $normalized = $this->normalizeNullableString($value);

        return $normalized ? mb_strtolower($normalized) : null;
    }
}

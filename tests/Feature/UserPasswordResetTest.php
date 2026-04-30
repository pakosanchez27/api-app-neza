<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\UserResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UserPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_a_reset_link(): void
    {
        Notification::fake();

        $role = Role::query()->create([
            'nombre' => 'Usuario',
        ]);

        $user = User::factory()->create([
            'email' => 'usuario@nezago.test',
            'id_rol' => $role->id_rol,
            'activo' => true,
        ]);

        $response = $this->postJson('/api/auth/usuarios/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Si encontramos una cuenta asociada a ese correo, te enviaremos un enlace para restablecer tu contrasena.',
        ]);

        Notification::assertSentTo($user, UserResetPasswordNotification::class);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $role = Role::query()->create([
            'nombre' => 'Usuario',
        ]);

        $user = User::factory()->create([
            'email' => 'usuario@nezago.test',
            'password' => Hash::make('password-viejo'),
            'id_rol' => $role->id_rol,
            'activo' => true,
        ]);

        $token = Password::broker('users')->createToken($user);

        $response = $this->postJson('/api/auth/usuarios/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'nuevo-password',
            'password_confirmation' => 'nuevo-password',
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Tu contrasena fue actualizada correctamente. Ya puedes iniciar sesion.',
        ]);

        $user->refresh();

        $this->assertTrue(Hash::check('nuevo-password', $user->password));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\CommerceResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class CommercePasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_commerce_can_request_reset_link_when_password_is_not_temporary(): void
    {
        Notification::fake();

        $role = Role::query()->create([
            'nombre' => 'AdminComercios',
        ]);

        $user = User::factory()->create([
            'email' => 'comercio@nezago.test',
            'id_rol' => $role->id_rol,
            'activo' => true,
            'is_password_templ' => false,
        ]);

        $response = $this->postJson('/api/auth/comercios/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Si encontramos una cuenta de comercio asociada a ese correo, te enviaremos un enlace para restablecer tu contrasena.',
        ]);

        Notification::assertSentTo($user, CommerceResetPasswordNotification::class);
    }

    public function test_commerce_with_temporary_password_is_told_to_review_email(): void
    {
        Notification::fake();

        $role = Role::query()->create([
            'nombre' => 'AdminComercios',
        ]);

        $user = User::factory()->create([
            'email' => 'temporal@nezago.test',
            'id_rol' => $role->id_rol,
            'activo' => true,
            'is_password_templ' => true,
        ]);

        $response = $this->postJson('/api/auth/comercios/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Tu cuenta aun usa una contrasena temporal. Revisa tu correo electronico y utiliza las instrucciones que recibiste para ingresar.',
        ]);

        Notification::assertNothingSent();
    }

    public function test_commerce_can_reset_password_with_valid_token(): void
    {
        $role = Role::query()->create([
            'nombre' => 'AdminComercios',
        ]);

        $user = User::factory()->create([
            'email' => 'comercio@nezago.test',
            'password' => Hash::make('password-viejo'),
            'id_rol' => $role->id_rol,
            'activo' => true,
            'is_password_templ' => false,
        ]);

        $token = Password::broker('users')->createToken($user);

        $response = $this->postJson('/api/auth/comercios/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'nuevo-password',
            'password_confirmation' => 'nuevo-password',
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Tu contrasena fue actualizada correctamente. Ya puedes iniciar sesion en tu cuenta de comercio.',
        ]);

        $user->refresh();

        $this->assertTrue(Hash::check('nuevo-password', $user->password));
        $this->assertFalse((bool) $user->is_password_templ);
    }
}

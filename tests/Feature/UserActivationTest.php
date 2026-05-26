<?php

namespace Tests\Feature;

use App\Mail\UserActivationMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_receives_a_six_digit_activation_code_when_registering(): void
    {
        Mail::fake();
        $this->createDefaultRoles();

        $response = $this->postJson('/api/auth/usuarios/registro', [
            'nombre_p' => 'Juan',
            'app_p' => 'Perez',
            'apm_p' => 'Lopez',
            'telefono' => '5512345678',
            'email' => 'juan@nezago.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()->assertJson([
            'email' => 'juan@nezago.test',
        ]);

        $user = User::query()->where('email', 'juan@nezago.test')->firstOrFail();

        $this->assertFalse((bool) $user->activo);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $user->token_activacion);

        Mail::assertSent(UserActivationMail::class, function (UserActivationMail $mail) use ($user) {
            return $mail->user->is($user)
                && preg_match('/^\d{6}$/', (string) $mail->user->token_activacion) === 1;
        });
    }

    public function test_user_can_activate_account_with_email_and_code(): void
    {
        $role = $this->createDefaultRoles()->last();

        $user = User::factory()->create([
            'email' => 'juan@nezago.test',
            'id_rol' => $role->id_rol,
            'activo' => false,
            'email_verified_at' => null,
            'token_activacion' => '123456',
        ]);

        $response = $this->postJson('/api/auth/usuarios/activar', [
            'email' => $user->email,
            'code' => '123456',
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Tu cuenta fue activada correctamente. Ya puedes iniciar sesion.',
        ]);

        $user->refresh();

        $this->assertTrue((bool) $user->activo);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->token_activacion);
    }

    private function createDefaultRoles()
    {
        return collect([
            Role::query()->create(['nombre' => 'Admin']),
            Role::query()->create(['nombre' => 'Comercio']),
            Role::query()->create(['nombre' => 'Usuario']),
        ]);
    }
}

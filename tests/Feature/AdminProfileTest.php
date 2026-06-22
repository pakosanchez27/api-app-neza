<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');
    }

    public function test_authenticated_admin_can_open_profile(): void
    {
        $response = $this->withSession($this->adminSession())
            ->get('/admin/perfil');

        $response->assertOk()
            ->assertSee('Mi perfil')
            ->assertSee('admin@example.com');
    }

    public function test_profile_is_sent_to_auth_service_and_session_is_updated(): void
    {
        config([
            'services.auth_api.url' => 'https://auth.example.test',
            'services.auth_api.profile_path' => '/api/auth/profile',
        ]);

        Http::fake([
            'https://auth.example.test/api/auth/profile' => Http::response([
                'data' => [
                    'user' => [
                        'name' => 'Nombre Actualizado',
                        'email' => 'nuevo@example.com',
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession($this->adminSession())->put('/admin/perfil', [
            'name' => 'Nombre Actualizado',
            'email' => 'nuevo@example.com',
        ]);

        $response->assertRedirect('/admin/perfil')
            ->assertSessionHas('sweet_alert.icon', 'success')
            ->assertSessionHas('admin_user.name', 'Nombre Actualizado')
            ->assertSessionHas('admin_user.email', 'nuevo@example.com');

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'PUT'
                && $request->hasHeader('Authorization', 'Bearer test-token')
                && $request->data() === [
                    'name' => 'Nombre Actualizado',
                    'email' => 'nuevo@example.com',
                ];
        });
    }

    public function test_password_is_changed_using_session_token_and_user_id(): void
    {
        config([
            'services.auth_api.url' => 'https://auth.example.test',
            'services.auth_api.password_path' => '/api/integrations/users/{id}/password',
        ]);

        Http::fake([
            'https://auth.example.test/api/integrations/users/7/password' => Http::response([
                'message' => 'La contrasena se actualizo correctamente.',
                'data' => ['user_id' => 7],
            ]),
        ]);

        $response = $this->withSession($this->adminSession())
            ->put('/admin/perfil/password', [
                'current_password' => 'contrasena-actual',
                'password' => 'nueva-contrasena',
                'password_confirmation' => 'nueva-contrasena',
            ]);

        $response->assertRedirect('/admin/perfil')
            ->assertSessionHas('sweet_alert.icon', 'success')
            ->assertSessionHas('sweet_alert.text', 'La contrasena se actualizo correctamente.');

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://auth.example.test/api/integrations/users/7/password'
                && $request->method() === 'PUT'
                && $request->hasHeader('Authorization', 'Bearer test-token')
                && $request->data() === [
                    'current_password' => 'contrasena-actual',
                    'password' => 'nueva-contrasena',
                ];
        });
    }

    public function test_new_password_requires_current_password_and_confirmation(): void
    {
        Http::fake();

        $response = $this->withSession($this->adminSession())
            ->from('/admin/perfil')
            ->put('/admin/perfil/password', [
                'password' => 'nueva-contrasena',
                'password_confirmation' => 'diferente',
            ]);

        $response->assertRedirect('/admin/perfil')
            ->assertSessionHasErrors(['current_password', 'password']);

        Http::assertNothingSent();
    }

    private function adminSession(): array
    {
        return [
            'admin_auth' => true,
            'admin_access_token' => 'test-token',
            'admin_user' => [
                'id' => 7,
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'activo' => true,
            ],
            'admin_permissions' => [],
        ];
    }
}

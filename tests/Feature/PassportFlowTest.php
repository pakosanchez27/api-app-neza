<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\Auth\CommercePassportController;
use App\Models\Establecimiento;
use App\Models\Role;
use App\Models\Ruta;
use App\Models\Tipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PassportFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_establishment_generates_qr_token_on_create(): void
    {
        $owner = User::factory()->create([
            'activo' => true,
            'id_rol' => $this->createRole('AdminComercios')->id_rol,
        ]);

        $tipo = Tipo::query()->create([
            'nombre' => 'Restaurantes',
        ]);

        $establecimiento = Establecimiento::query()->create([
            'nombre_est' => 'Tacos del Centro',
            'user_id' => $owner->id,
            'id_tipo' => $tipo->id_tipo,
            'descripcion' => 'Prueba',
            'is_route' => true,
            'is_visible' => true,
            'estatus' => true,
        ]);

        $this->assertNotEmpty($establecimiento->qr_token);
        $this->assertStringStartsWith('NEZA-QR-', $establecimiento->qr_token);
    }

    public function test_commerce_can_request_dynamic_passport_qr(): void
    {
        [$commerceUser, $establecimiento, $ruta] = $this->createRouteCommerceContext();

        Sanctum::actingAs($commerceUser);

        $response = $this->getJson('/api/auth/comercios/pasaporte/qr');

        $response->assertOk()
            ->assertJsonPath('route.id_ruta', $ruta->id_ruta)
            ->assertJsonPath('establishment.id_establecimiento', $establecimiento->id_establecimiento);

        $token = $response->json('qr.token');

        $this->assertNotEmpty($token);
        $this->assertNotNull(CommercePassportController::parseDynamicQrToken($token));
    }

    public function test_user_can_seal_passport_with_dynamic_qr(): void
    {
        [$commerceUser, $establecimiento, $ruta] = $this->createRouteCommerceContext();
        $traveler = User::factory()->create([
            'activo' => true,
            'id_rol' => $this->createRole('Usuario')->id_rol,
        ]);

        Sanctum::actingAs($commerceUser);
        $qrResponse = $this->getJson('/api/auth/comercios/pasaporte/qr');
        $dynamicToken = $qrResponse->json('qr.token');

        Sanctum::actingAs($traveler);
        $sealResponse = $this->postJson("/api/auth/usuarios/rutas/{$ruta->id_ruta}/pasaporte/sellar", [
            'qr_token' => $dynamicToken,
        ]);

        $sealResponse->assertOk()
            ->assertJsonPath('passport.passport.visited_count', 1)
            ->assertJsonPath('passport.passport.total_count', 1)
            ->assertJsonPath('passport.stamps.0.id_establecimiento', $establecimiento->id_establecimiento)
            ->assertJsonPath('passport.stamps.0.status', 'visitado');
    }

    public function test_user_cannot_seal_same_establishment_twice(): void
    {
        [$commerceUser, , $ruta] = $this->createRouteCommerceContext();
        $traveler = User::factory()->create([
            'activo' => true,
            'id_rol' => $this->createRole('Usuario')->id_rol,
        ]);

        Sanctum::actingAs($commerceUser);
        $dynamicToken = $this->getJson('/api/auth/comercios/pasaporte/qr')->json('qr.token');

        Sanctum::actingAs($traveler);

        $this->postJson("/api/auth/usuarios/rutas/{$ruta->id_ruta}/pasaporte/sellar", [
            'qr_token' => $dynamicToken,
        ])->assertOk();

        $duplicateResponse = $this->postJson("/api/auth/usuarios/rutas/{$ruta->id_ruta}/pasaporte/sellar", [
            'qr_token' => $dynamicToken,
        ]);

        $duplicateResponse->assertStatus(409)
            ->assertJsonPath('message', 'Este establecimiento ya fue sellado en tu pasaporte.');
    }

    private function createRouteCommerceContext(): array
    {
        $commerceRole = $this->createRole('AdminComercios');
        $tipo = Tipo::query()->create([
            'nombre' => 'Restaurantes',
        ]);

        $commerceUser = User::factory()->create([
            'activo' => true,
            'id_rol' => $commerceRole->id_rol,
        ]);

        $establecimiento = Establecimiento::query()->create([
            'nombre_est' => 'Birria de Prueba',
            'user_id' => $commerceUser->id,
            'id_tipo' => $tipo->id_tipo,
            'descripcion' => 'Establecimiento de prueba',
            'is_route' => true,
            'is_visible' => true,
            'estatus' => true,
        ]);

        $ruta = Ruta::query()->create([
            'nombre' => 'Ruta Demo',
            'slug' => 'ruta-demo',
            'descripcion' => 'Ruta para pruebas',
            'is_active' => true,
        ]);

        $ruta->establecimientos()->attach($establecimiento->id_establecimiento, [
            'orden' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$commerceUser, $establecimiento, $ruta];
    }

    private function createRole(string $name): Role
    {
        return Role::query()->firstOrCreate([
            'nombre' => $name,
        ]);
    }
}

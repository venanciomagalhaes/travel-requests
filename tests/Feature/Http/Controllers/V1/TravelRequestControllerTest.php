<?php

namespace Tests\Feature\Http\Controllers\V1;

use App\Enums\V1\Role\RolesNamesEnum;
use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use App\Models\Role;
use App\Models\TravelRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

describe('Store Travel Request', function () {

    beforeEach(function () {
        $this->usuario = User::factory()->create();
    });

    test('deve criar um pedido de viagem com sucesso quando os dados são válidos', function () {
        $dados = [
            'travelers_name' => 'John Doe',
            'destination' => 'New York',
            'departure_date' => now()->addDays(1)->format('Y-m-d'),
            'return_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->usuario, 'api')
            ->postJson(route('api.v1.travel-requests.store'), $dados);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('travelers_name', 'John Doe')
            ->assertJsonPath('status', 'requested')
            ->assertJsonStructure([
                'uuid',
                'travelers_name',
                'destination',
                'departure_date',
                'return_date',
                'status',
            ]);

        $this->assertDatabaseHas('travel_requests', [
            'travelers_name' => 'John Doe',
            'user_id' => $this->usuario->id,
            'status' => 'requested',
        ]);
    });

    test('deve falhar a validação se campos obrigatórios estiverem ausentes', function () {
        $response = $this->actingAs($this->usuario, 'api')
            ->postJson(route('api.v1.travel-requests.store'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'travelers_name',
                'destination',
                'departure_date',
                'return_date',
            ]);
    });

    test('deve falhar se a data de retorno for anterior à data de ida', function () {
        $dados = [
            'travelers_name' => 'John Doe',
            'destination' => 'Paris',
            'departure_date' => now()->addDays(10)->format('Y-m-d'),
            'return_date' => now()->addDays(5)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->usuario, 'api')
            ->postJson(route('api.v1.travel-requests.store'), $dados);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['return_date']);
    });
});

describe('Store Protection', function () {

    test('não deve permitir acesso à criação de pedidos sem token', function () {
        $this->postJson(route('api.v1.travel-requests.store'), [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
});

describe('Show Travel Request', function () {

    beforeEach(function () {
        $this->usuario = User::factory()->create();
    });

    test('deve exibir os detalhes do pedido se ele pertencer ao usuário autenticado', function () {
        $pedido = TravelRequest::factory()->create([
            'user_id' => $this->usuario->id,
            'travelers_name' => 'John Doe',
        ]);

        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.show', $pedido->uuid));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'uuid' => $pedido->uuid,
                'travelers_name' => 'John Doe',
            ])
            ->assertJsonStructure([
                'uuid',
                'travelers_name',
                'destination',
                'status',
            ]);
    });

    test('deve retornar 404 se o pedido pertencer a outro usuário', function () {
        $outroUsuario = User::factory()->create();
        $pedidoDeOutro = TravelRequest::factory()->create([
            'user_id' => $outroUsuario->id,
        ]);

        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.show', $pedidoDeOutro->uuid));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });

    test('deve retornar 404 para um UUID que não existe na base', function () {
        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.show', 'uuid-inexistente-123'));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });
});

/**
 * Cenários de Proteção de Rota
 */
describe('Show Middleware Protection', function () {

    test('não deve permitir visualizar detalhes sem estar autenticado', function () {
        $pedido = TravelRequest::factory()->create();

        $this->getJson(route('api.v1.travel-requests.show', $pedido->uuid))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
});

describe('Index Travel Request', function () {

    beforeEach(function () {
        $this->usuario = User::factory()->create();
    });

    test('deve listar apenas os pedidos pertencentes ao usuário autenticado', function () {
        TravelRequest::factory()->count(3)->create(['user_id' => $this->usuario->id]);

        $outroUsuario = User::factory()->create();
        TravelRequest::factory()->create(['user_id' => $outroUsuario->id]);

        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.index'));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data');
    });

    test('deve filtrar por status, destino e nome (Requisitos do Desafio)', function () {
        TravelRequest::factory()->create([
            'user_id' => $this->usuario->id,
            'status' => 'approved',
            'destination' => 'França',
            'travelers_name' => 'John Doe',
        ]);

        $query = [
            'status' => 'approved',
            'destination' => 'França',
            'travelers_name' => 'John',
        ];

        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.index', $query));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.destination', 'França');
    });

    test('deve filtrar por período de tempo (Data de Ida e Volta)', function () {
        TravelRequest::factory()->create([
            'user_id' => $this->usuario->id,
            'departure_date' => '2026-05-10',
            'return_date' => '2026-05-20',
        ]);

        TravelRequest::factory()->create([
            'user_id' => $this->usuario->id,
            'departure_date' => '2026-06-01',
            'return_date' => '2026-06-10',
        ]);

        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.index', [
                'departure_date' => '2026-05-01',
                'return_date' => '2026-05-25',
            ]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');
    });

    test('deve respeitar a paginação e retornar metadados', function () {
        TravelRequest::factory()->count(10)->create(['user_id' => $this->usuario->id]);

        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.index', ['per_page' => 5]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data',
                'links',
                'current_page', 'last_page', 'per_page', 'total',
            ]);
    });

    test('deve retornar 204 No Content quando a busca não encontrar resultados', function () {
        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.index', ['status' => 'canceled']));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    });

    test('deve falhar se os formatos de data na busca forem inválidos', function () {
        $response = $this->actingAs($this->usuario, 'api')
            ->getJson(route('api.v1.travel-requests.index', ['departure_date' => '10/05/2026']));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['departure_date']);
    });
});

describe('Index Middleware Protection', function () {

    test('não deve permitir listar pedidos sem estar autenticado', function () {
        $this->getJson(route('api.v1.travel-requests.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
});

describe('Change Status Travel Request', function () {

    beforeEach(function () {
        $this->adminRole = Role::query()->where('name', RolesNamesEnum::ADMINISTRATOR->value)->first();

        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
        ]);
    });

    test('deve permitir que um administrador aprove um pedido de viagem', function () {
        $pedido = TravelRequest::factory()->create([
            'status' => TravelRequestStatusEnum::REQUESTED->value,
        ]);

        $dados = ['status' => TravelRequestStatusEnum::APPROVED->value];

        $response = $this->actingAs($this->adminUser, 'api')
            ->patchJson(route('api.v1.travel-requests.status', $pedido->uuid), $dados);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('status', TravelRequestStatusEnum::APPROVED->value);

        $this->assertDatabaseHas('travel_requests', [
            'uuid' => $pedido->uuid,
            'status' => TravelRequestStatusEnum::APPROVED->value,
        ]);
    });

    test('deve retornar conflito se tentar alterar um pedido que já está aprovado', function () {
        $pedido = TravelRequest::factory()->create([
            'status' => TravelRequestStatusEnum::APPROVED->value,
        ]);

        $dados = ['status' => TravelRequestStatusEnum::CANCELED->value];

        $response = $this->actingAs($this->adminUser, 'api')
            ->patchJson(route('api.v1.travel-requests.status', $pedido->uuid), $dados);

        $response->assertStatus(Response::HTTP_CONFLICT)
            ->assertJsonFragment(['message' => 'Travel request already approved and cannot be changed.']);
    });

    test('deve falhar a validação se o status enviado for inválido', function () {
        $pedido = TravelRequest::factory()->create();

        $response = $this->actingAs($this->adminUser, 'api')
            ->patchJson(route('api.v1.travel-requests.status', $pedido->uuid), [
                'status' => 'invalid-status',
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['status']);
    });
});

describe('Status Protection', function () {

    test('não deve permitir que um usuário comum altere o status de um pedido', function () {
        $customerRole = Role::query()->where('name', RolesNamesEnum::CUSTOMER->value)->first();
        $usuarioComum = User::factory()->create(['role_id' => $customerRole->id]);

        $pedido = TravelRequest::factory()->create();

        $response = $this->actingAs($usuarioComum, 'api')
            ->patchJson(route('api.v1.travel-requests.status', $pedido->uuid), [
                'status' => TravelRequestStatusEnum::APPROVED->value,
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });

    test('não deve permitir alterar status sem estar autenticado', function () {
        $pedido = TravelRequest::factory()->create();

        $this->patchJson(route('api.v1.travel-requests.status', $pedido->uuid), [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
});

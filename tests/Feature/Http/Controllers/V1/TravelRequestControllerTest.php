<?php

namespace Tests\Feature\Http\Controllers\V1;

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

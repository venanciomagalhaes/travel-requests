<?php

namespace Tests\Feature\Http\Controllers\V1;

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

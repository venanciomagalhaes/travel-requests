<?php

namespace Tests\Feature\Http\Controllers\V1;

use App\Enums\V1\Role\RolesNamesEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cenários de Registro (POST /register)
 */
describe('Register', function () {

    test('deve retornar 422 se o usuário for criado simultaneamente ou já existir na base', function () {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson(route('api.v1.auth.register'), [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $status = $response->getStatusCode();

        expect($status)->toBeIn([Response::HTTP_UNPROCESSABLE_ENTITY]);

    });

    test('deve registrar um usuário com sucesso quando os dados são válidos e atribuir a role customer', function () {
        $customerRole = Role::where('name', RolesNamesEnum::CUSTOMER->value)->first();

        $response = $this->postJson(route('api.v1.auth.register'), [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['access_token', 'token_type']);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role_id' => $customerRole->id,
        ]);

        $user = User::where('email', 'newuser@example.com')->first();
        expect($user->role_id)->toBe($customerRole->id);
    });

    test('deve falhar a validação se campos obrigatórios estiverem ausentes', function () {
        $response = $this->postJson(route('api.v1.auth.register'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    });

    test('deve falhar se o e-mail já estiver em uso', function () {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson(route('api.v1.auth.register'), [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    });
});

/**
 * Cenários de Login (POST /login)
 */
describe('Login', function () {

    test('deve autenticar com sucesso e retornar um token JWT', function () {
        User::factory()->create([
            'email' => 'dev@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => 'dev@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['access_token', 'token_type']);
    });

    test('deve retornar erro 401 para credenciais inválidas', function () {
        User::factory()->create(['email' => 'user@example.com']);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
});

/**
 * Cenários de Rotas Autenticadas (Me, Refresh, Logout)
 */
describe('Authenticated Routes', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    });

    test('deve retornar os dados do perfil do usuário autenticado', function () {

        $response = $this->getJson(route('api.v1.auth.me'));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'name',
                'email',
                'role',
            ]);
    });

    test('deve renovar o token com sucesso', function () {

        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $response = $this->withToken($token)->getJson(route('api.v1.auth.refresh'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['access_token']);
    });

    test('deve invalidar o token ao fazer logout', function () {
        $response = $this->deleteJson(route('api.v1.auth.logout'));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->getJson(route('api.v1.auth.me'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
});

/**
 * Cenários de Erro de Acesso
 */
test('não deve permitir acesso a rotas privadas sem token', function () {
    $this->getJson(route('api.v1.auth.me'))->assertStatus(Response::HTTP_UNAUTHORIZED);
    $this->getJson(route('api.v1.auth.refresh'))->assertStatus(Response::HTTP_UNAUTHORIZED);
    $this->deleteJson(route('api.v1.auth.logout'))->assertStatus(Response::HTTP_UNAUTHORIZED);
});

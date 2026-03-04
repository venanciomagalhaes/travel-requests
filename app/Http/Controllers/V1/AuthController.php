<?php

namespace App\Http\Controllers\V1;

use App\Actions\V1\Auth\LoginAction;
use App\Actions\V1\Auth\RegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Dto\V1\Auth\LoginDto;
use App\Http\Dto\V1\Auth\RegisterDto;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginAction $loginAction,
        private readonly RegisterAction $registerAction,
    ) {}

    #[OA\Post(
        path: '/api/v1/auth/register',
        summary: 'Registrar um novo usuário',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest')
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuário registrado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'access_token', type: 'string'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                        new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Erro de validação'),
            new OA\Response(response: 409, description: 'Conflito: Usuário já existe'),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterDto::fromRequest($request);
        $token = $this->registerAction->handle($dto);

        return $this->respondWithToken($token, Response::HTTP_CREATED);
    }

    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Autenticação de usuário',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login realizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'access_token', type: 'string'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                        new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciais inválidas'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDto::fromRequest($request);
        $token = $this->loginAction->handle($dto);

        return $this->respondWithToken($token);
    }

    #[OA\Get(
        path: '/api/v1/auth/me',
        summary: 'Dados do usuário logado',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Dados do perfil obtidos',
                content: new OA\JsonContent(ref: '#/components/schemas/UserResource')
            ),
            new OA\Response(response: 401, description: 'Não autorizado'),
        ]
    )]
    public function me(): JsonResponse
    {
        return response()->json(UserResource::make(auth()->user()));
    }

    #[OA\Delete(
        path: '/api/v1/auth/logout',
        summary: 'Encerrar sessão',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 204, description: 'Logout realizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autorizado'),
        ]
    )]
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out'], Response::HTTP_NO_CONTENT);
    }

    #[OA\Get(
        path: '/api/v1/auth/refresh',
        summary: 'Renovar token JWT',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token renovado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'access_token', type: 'string'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                        new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token inválido ou expirado'),
        ]
    )]
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], $status);
    }
}

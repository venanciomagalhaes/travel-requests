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
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginAction $loginAction,
        private readonly RegisterAction $registerAction,
    ) {}

    /**
     * @throws Throwable
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterDto::fromRequest($request);
        $token = $this->registerAction->handle($dto);

        return $this->respondWithToken($token, Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDto::fromRequest($request);
        $token = $this->loginAction->handle($dto);

        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        return response()->json(UserResource::make(auth()->user()));
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out'], Response::HTTP_NO_CONTENT);
    }

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

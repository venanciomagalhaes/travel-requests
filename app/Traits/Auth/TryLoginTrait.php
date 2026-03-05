<?php

namespace App\Traits\Auth;

use App\Services\Jwt\JwtServiceInterface;
use Symfony\Component\HttpFoundation\Response;

trait TryLoginTrait
{
    private function tryLogin(string $email, string $password): string|bool
    {
        $jwtService = app(JwtServiceInterface::class);

        return $jwtService->attempt($email, $password);
    }

    private function throwExceptionIfUnauthorized(bool|string $token): void
    {
        if (! $token) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }
    }
}

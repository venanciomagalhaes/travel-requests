<?php

namespace App\Traits\Auth;

use App\Exceptions\BusinessException;
use App\Services\Jwt\JwtServiceInterface;
use Symfony\Component\HttpFoundation\Response;

trait TryLoginTrait
{
    private function tryLogin(string $email, string $password): string|bool
    {
        $jwtService = app(JwtServiceInterface::class);

        return $jwtService->attempt($email, $password);
    }

    /**
     * @throws BusinessException
     */
    private function throwExceptionIfUnauthorized(bool|string $token): void
    {
        if (! $token) {
            throw new BusinessException('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }
    }
}

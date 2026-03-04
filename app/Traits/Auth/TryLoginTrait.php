<?php

namespace App\Traits\Auth;

use Symfony\Component\HttpFoundation\Response;

trait TryLoginTrait
{
    private function tryLogin(string $email, string $password): string|bool
    {
        return auth('api')->attempt(['email' => $email, 'password' => $password]);
    }

    private function throwExceptionIfUnauthorized(bool|string $token): void
    {
        if (! $token) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }
    }
}

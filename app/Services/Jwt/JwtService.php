<?php

namespace App\Services\Jwt;

class JwtService implements JwtServiceInterface
{
    public function attempt(string $email, string $password): string|bool
    {
        return auth('api')->attempt(['email' => $email, 'password' => $password]);
    }
}

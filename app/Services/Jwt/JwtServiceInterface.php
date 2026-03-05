<?php

namespace App\Services\Jwt;

interface JwtServiceInterface
{
    public function attempt(string $email, string $password): string|bool;
}

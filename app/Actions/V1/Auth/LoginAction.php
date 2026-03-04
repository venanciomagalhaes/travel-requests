<?php

namespace App\Actions\V1\Auth;

use App\Http\Dto\V1\Auth\LoginDto;
use App\Traits\Auth\TryLoginTrait;

readonly class LoginAction
{
    use TryLoginTrait;

    public function handle(LoginDto $dto): string
    {
        $token = $this->tryLogin(email: $dto->getEmail(), password: $dto->getPassword());
        $this->throwExceptionIfUnauthorized($token);

        return $token;
    }
}

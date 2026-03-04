<?php

namespace App\Http\Dto\V1\Auth;

use App\Http\Dto\V1\DtoInterface;
use Illuminate\Foundation\Http\FormRequest;

readonly class LoginDto implements DtoInterface
{
    private function __construct(
        private string $email,
        private string $password,
    ) {}

    public static function fromRequest(FormRequest $request): LoginDto
    {
        return new self(
            email: $request->input('email'),
            password: $request->input('password'),
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}

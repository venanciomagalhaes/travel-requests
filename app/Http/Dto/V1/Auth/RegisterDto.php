<?php

namespace App\Http\Dto\V1\Auth;

use App\Http\Dto\V1\DtoInterface;
use Illuminate\Foundation\Http\FormRequest;

readonly class RegisterDto implements DtoInterface
{
    private function __construct(
        private string $name,
        private string $email,
        private string $password,
    ) {}

    public static function fromRequest(FormRequest $request): RegisterDto
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
    }

    public function getName(): string
    {
        return $this->name;
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

<?php

namespace App\Repositories\V1\User;

use App\Http\Dto\V1\Auth\RegisterDto;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(RegisterDto $dto, int $roleId): User;

    public function findByEmail(string $email): ?User;
}

<?php

namespace App\Repositories\V1\User;

use App\Http\Dto\V1\Auth\RegisterDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function create(RegisterDto $dto, int $roleId): User
    {
        return User::query()->create([
            'uuid' => Str::uuid()->toString(),
            'name' => $dto->getName(),
            'email' => $dto->getEmail(),
            'password' => Hash::make($dto->getPassword()),
            'role_id' => $roleId,
        ])->load('role');
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->with('role')->where('email', $email)->first();
    }
}

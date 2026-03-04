<?php

namespace App\Actions\V1\Auth;

use App\Enums\V1\Role\RolesNamesEnum;
use App\Http\Dto\V1\Auth\RegisterDto;
use App\Models\User;
use App\Repositories\V1\Role\RoleRepositoryInterface;
use App\Repositories\V1\User\UserRepositoryInterface;
use App\Traits\Auth\TryLoginTrait;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

readonly class RegisterAction
{
    use TryLoginTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(RegisterDto $dto): string
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->userRepository->findByEmail($dto->getEmail());
            $this->throwExceptionIfUserAlreadyExists($user);
            $role = $this->roleRepository->findByName(RolesNamesEnum::CUSTOMER);
            $this->userRepository->create($dto, $role->id);
            $token = $this->tryLogin(email: $dto->getEmail(), password: $dto->getPassword());
            $this->throwExceptionIfUnauthorized($token);

            return $token;
        });
    }

    private function throwExceptionIfUserAlreadyExists(?User $user): void
    {
        if ($user) {
            abort(Response::HTTP_CONFLICT, 'This email is already registered');
        }
    }
}

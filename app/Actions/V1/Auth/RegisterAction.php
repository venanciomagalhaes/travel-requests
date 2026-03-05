<?php

namespace App\Actions\V1\Auth;

use App\Enums\V1\Role\RolesNamesEnum;
use App\Http\Dto\V1\Auth\RegisterDto;
use App\Models\User;
use App\Repositories\V1\Role\RoleRepositoryInterface;
use App\Repositories\V1\User\UserRepositoryInterface;
use App\Services\Logger\LoggerServiceInterface;
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
        private LoggerServiceInterface $logger,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(RegisterDto $dto): string
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->userRepository->findByEmail($dto->getEmail());

            $this->throwExceptionIfUserAlreadyExists($user, $dto->getEmail());

            $role = $this->roleRepository->findByName(RolesNamesEnum::CUSTOMER);

            $this->userRepository->create($dto, $role->id);

            $this->logger->info("User [{$dto->getEmail()}] successfully registered and role assigned.");

            $token = $this->tryLogin(email: $dto->getEmail(), password: $dto->getPassword());
            if (! $token) {
                $this->logger->warning('Unauthorized login attempt', ['email' => $dto->getEmail()]);
                $this->throwExceptionIfUnauthorized($token);
            }

            return $token;
        });
    }

    private function throwExceptionIfUserAlreadyExists(?User $user, string $email): void
    {
        if ($user) {
            $this->logger->warning("Registration failed: Email [{$email}] is already in use.");
            abort(Response::HTTP_CONFLICT, 'This email is already registered');
        }
    }
}

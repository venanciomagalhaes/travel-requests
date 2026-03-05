<?php

namespace App\Actions\V1\Auth;

use App\Http\Dto\V1\Auth\LoginDto;
use App\Services\Logger\LoggerServiceInterface;
use App\Traits\Auth\TryLoginTrait;

readonly class LoginAction
{
    use TryLoginTrait;

    public function __construct(
        private LoggerServiceInterface $logger
    ) {}

    public function handle(LoginDto $dto): string
    {
        $token = $this->tryLogin($dto->getEmail(), $dto->getPassword());
        if (! $token) {
            $this->logger->warning('Unauthorized login attempt', ['email' => $dto->getEmail()]);
            $this->throwExceptionIfUnauthorized($token);
        }
        $this->logger->info('User authenticated successfully', ['email' => $dto->getEmail()]);

        return $token;
    }
}

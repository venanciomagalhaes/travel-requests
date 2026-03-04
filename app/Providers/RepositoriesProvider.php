<?php

namespace App\Providers;

use App\Repositories\V1\Role\RoleRepository;
use App\Repositories\V1\Role\RoleRepositoryInterface;
use App\Repositories\V1\User\UserRepository;
use App\Repositories\V1\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoriesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

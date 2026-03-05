<?php

namespace App\Providers;

use App\Exceptions\AppExceptionHandler;
use App\Services\Jwt\JwtService;
use App\Services\Jwt\JwtServiceInterface;
use App\Services\Logger\LoggerService;
use App\Services\Logger\LoggerServiceInterface;
use App\Services\Notification\NotificationService;
use App\Services\Notification\NotificationServiceInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
        $this->app->bind(LoggerServiceInterface::class, LoggerService::class);
        $this->app->bind(JwtServiceInterface::class, JwtService::class);
        $this->app->bind(ExceptionHandler::class, AppExceptionHandler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Services\Logger;

use Illuminate\Support\Facades\Log;

class LoggerService implements LoggerServiceInterface
{
    public function error(string $message, array $context = []): void
    {
        if (! app()->environment('testing')) {
            Log::error($message, $context);
        }
    }

    public function warning(string $message, array $context = []): void
    {
        if (! app()->environment('testing')) {
            Log::warning($message, $context);
        }
    }

    public function info(string $message, array $context = []): void
    {
        if (! app()->environment('testing')) {
            Log::info($message, $context);
        }
    }

    public function debug(string $message, array $context = []): void
    {
        if (! app()->environment('testing')) {
            Log::debug($message, $context);
        }
    }

    public function log(string $level, string $message, array $context = []): void
    {
        if (! app()->environment('testing')) {
            Log::log($level, $message, $context);
        }
    }
}

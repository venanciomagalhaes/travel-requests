<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AppExceptionHandler extends Handler
{
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof ModelNotFoundException) {
            return response()->json('Resource not found', Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof BusinessException) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

        return parent::render($request, $e);
    }
}

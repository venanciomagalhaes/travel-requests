<?php

namespace App\Http\Dto\V1;

use Illuminate\Foundation\Http\FormRequest;

interface DtoInterface
{
    public static function fromRequest(FormRequest $request);
}

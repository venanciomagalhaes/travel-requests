<?php

namespace App\Http\Requests\V1\Auth;

use App\Enums\V1\Feature\FeaturesNamesEnum;
use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasFeature(FeaturesNamesEnum::LOGOUT);
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}

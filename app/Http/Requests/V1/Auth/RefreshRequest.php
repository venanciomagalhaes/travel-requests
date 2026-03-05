<?php

namespace App\Http\Requests\V1\Auth;

use App\Enums\V1\Feature\FeaturesNamesEnum;
use Illuminate\Foundation\Http\FormRequest;

class RefreshRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasFeature(FeaturesNamesEnum::REFRESH);
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}

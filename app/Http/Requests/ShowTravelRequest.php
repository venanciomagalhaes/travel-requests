<?php

namespace App\Http\Requests;

use App\Enums\V1\Feature\FeaturesNamesEnum;
use Illuminate\Foundation\Http\FormRequest;

class ShowTravelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasFeature(FeaturesNamesEnum::SHOW_TRAVEL_REQUESTS);
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}

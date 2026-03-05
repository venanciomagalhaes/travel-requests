<?php

namespace App\Http\Requests\V1\TravelRequest;

use App\Enums\V1\Feature\FeaturesNamesEnum;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'StoreTravelRequest',
    description: 'Payload for creating a new travel request',
    required: ['travelers_name', 'destination', 'departure_date', 'return_date'],
    properties: [
        new OA\Property(property: 'travelers_name', description: "Requester's name", type: 'string', example: 'John Doe'),
        new OA\Property(property: 'destination', description: 'Travel destination', type: 'string', example: 'New York'),
        new OA\Property(property: 'departure_date', description: 'Departure date (Y-m-d)', type: 'string', format: 'date', example: '2026-05-15'),
        new OA\Property(property: 'return_date', description: 'Return date (Y-m-d)', type: 'string', format: 'date', example: '2026-05-22'),
    ],
    type: 'object'
)]
class StoreTravelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasFeature(FeaturesNamesEnum::STORE_TRAVEL_REQUESTS);
    }

    public function rules(): array
    {
        return [
            'travelers_name' => ['required', 'string', 'max:255', 'min:3'],
            'destination' => ['required', 'string', 'max:255', 'min:3'],
            'departure_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:departure_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'travelers_name.required' => 'The traveler’s name is required.',
            'travelers_name.min' => 'The traveler’s name must be at least 3 characters.',
            'destination.required' => 'The destination is required.',
            'departure_date.required' => 'The departure date is required.',
            'departure_date.after_or_equal' => 'The departure date cannot be in the past.',
            'return_date.required' => 'The return date is required.',
            'return_date.after_or_equal' => 'The return date must be equal to or after the departure date.',
            'date_format' => 'The date format must be Year-Month-Day (e.g., 2026-12-31).',
        ];
    }
}

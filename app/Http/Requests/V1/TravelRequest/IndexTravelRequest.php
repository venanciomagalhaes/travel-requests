<?php

namespace App\Http\Requests\V1\TravelRequest;

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'IndexTravelRequest',
    title: 'Index Travel Request Filters',
    description: 'Parâmetros de filtro passados via Query String para listagem de pedidos',
    properties: [
        new OA\Property(property: 'per_page', type: 'integer', example: 15, nullable: true),
        new OA\Property(property: 'status', type: 'string', example: 'requested', nullable: true, enum: ['requested', 'approved', 'canceled']),
        new OA\Property(property: 'travelers_name', type: 'string', example: 'John Doe', nullable: true, maxLength: 255),
        new OA\Property(property: 'destination', type: 'string', example: 'New York', nullable: true, maxLength: 255),
        new OA\Property(property: 'departure_date', type: 'string', format: 'date', example: '2026-05-15', nullable: true),
        new OA\Property(property: 'return_date', type: 'string', format: 'date', example: '2026-05-22', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date', example: '2026-01-01', nullable: true),
    ],
    type: 'object'
)]
class IndexTravelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', Rule::in([
                TravelRequestStatusEnum::REQUESTED->value,
                TravelRequestStatusEnum::CANCELED->value,
                TravelRequestStatusEnum::APPROVED->value,
            ])],
            'travelers_name' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'departure_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'return_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:departure_date'],
            'created_at' => ['nullable', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'The per page field must be an integer.',
            'per_page.min' => 'The per page value must be at least 1.',
            'per_page.max' => 'The per page value may not be greater than 100.',
            'status.in' => 'The selected status is invalid. Use: requested, approved or canceled.',
            'departure_date.date_format' => 'The departure date does not match the format Y-m-d.',
            'return_date.date_format' => 'The return date does not match the format Y-m-d.',
            'return_date.after_or_equal' => 'The return date must be equal to or after the departure date.',
            'created_at.date_format' => 'The created at field must be in Y-m-d format.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->query());
    }
}

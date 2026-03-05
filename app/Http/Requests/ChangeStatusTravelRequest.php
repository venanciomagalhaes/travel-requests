<?php

namespace App\Http\Requests;

use App\Enums\V1\Feature\FeaturesNamesEnum;
use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'ChangeStatusTravelRequest',
    description: 'Payload para alteração de status de um pedido de viagem',
    required: ['status'],
    properties: [
        new OA\Property(
            property: 'status',
            description: 'Novo status do pedido de viagem',
            type: 'string',
            example: 'approved',
            enum: ['approved', 'canceled']
        ),
    ],
    type: 'object'
)]
class ChangeStatusTravelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasFeature(FeaturesNamesEnum::CHANGE_STATUS_TRAVEL_REQUESTS);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                TravelRequestStatusEnum::APPROVED->value,
                TravelRequestStatusEnum::CANCELED->value,
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The status field is mandatory.',
            'status.in' => 'The selected status is invalid. Only "approved" or "canceled" are allowed.',
        ];
    }
}

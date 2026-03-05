<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'TravelRequestResource',
    description: 'Travel request details',
    properties: [
        new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000'),
        new OA\Property(property: 'travelers_name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'destination', type: 'string', example: 'New York'),
        new OA\Property(property: 'departure_date', type: 'string', format: 'date-time', example: '2026-05-15 08:00:00'),
        new OA\Property(property: 'return_date', type: 'string', format: 'date-time', example: '2026-05-22 18:00:00'),
        new OA\Property(property: 'status', type: 'string', example: 'requested', enum: ['requested', 'approved', 'canceled']),
    ],
    type: 'object'
)]
class TravelRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'travelers_name' => $this->resource->travelers_name,
            'destination' => $this->resource->destination,
            'departure_date' => $this->resource->departure_date->format('Y-m-d'),
            'return_date' => $this->resource->return_date->format('Y-m-d'),
            'status' => $this->resource->status,
        ];
    }
}

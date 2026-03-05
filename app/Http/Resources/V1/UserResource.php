<?php

namespace App\Http\Resources\V1;

use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserResource',
    title: 'User Resource',
    description: 'Esquema de dados do perfil do usuário',
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            example: 'João Silva'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            example: 'joao@example.com'
        ),
        new OA\Property(
            property: 'role',
            type: 'string',
            example: 'customer'
        ),
        new OA\Property(
            property: 'permissions',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'create-travel-request'),
                    new OA\Property(property: 'description', type: 'string', example: 'Permite criar novos pedidos de viagem'),
                ]
            )
        ),
    ]
)]
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'role' => $this->resource->role->name,
            'permissions' => $this->resource->role->features->map(function (Feature $feature) {
                return [
                    'name' => $feature->name,
                    'description' => $feature->description,
                ];
            }),
        ];
    }
}

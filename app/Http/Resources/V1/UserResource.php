<?php

namespace App\Http\Resources\V1;

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
        ];
    }
}

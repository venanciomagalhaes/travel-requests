<?php

namespace App\Http\Controllers\V1;

use App\Actions\V1\TravelRequest\ShowTravelRequestAction;
use App\Actions\V1\TravelRequest\StoreTravelRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Dto\V1\TravelRequest\StoreTravelRequestDTO;
use App\Http\Requests\V1\TravelRequest\StoreTravelRequest;
use App\Http\Resources\V1\TravelRequestResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TravelRequestController extends Controller
{
    public function __construct(
        private readonly StoreTravelRequestAction $storeTravelRequestAction,
        private readonly ShowTravelRequestAction $showTravelRequestAction,
    ) {}

    #[OA\Post(
        path: '/api/v1/travel-requests',
        description: 'Registers a new corporate travel request in the system.',
        summary: 'Create a new travel request',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreTravelRequest')
        ),
        tags: ['Travel Requests'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Travel request created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/TravelRequestResource')
            ),
        ]
    )]
    public function store(StoreTravelRequest $request)
    {
        $dto = StoreTravelRequestDTO::fromRequest($request);
        $travelRequest = $this->storeTravelRequestAction->handle($dto);

        return response()->json(
            TravelRequestResource::make($travelRequest),
            Response::HTTP_CREATED
        );
    }

    #[OA\Get(
        path: '/api/v1/travel-requests/{uuid}',
        summary: 'Exibir detalhes de um pedido',
        tags: ['Travel Requests'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sucesso', content: new OA\JsonContent(ref: '#/components/schemas/TravelRequestResource')),
            new OA\Response(response: 404, description: 'Pedido não encontrado ou não pertence ao usuário'),
        ]
    )]
    public function show(string $uuid)
    {
        $travelRequest = $this->showTravelRequestAction->handle($uuid);

        return response()->json(
            TravelRequestResource::make($travelRequest),
            Response::HTTP_OK
        );
    }
}

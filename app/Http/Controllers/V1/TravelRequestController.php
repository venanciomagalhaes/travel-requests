<?php

namespace App\Http\Controllers\V1;

use App\Actions\V1\TravelRequest\ChangeStatusTravelRequestAction;
use App\Actions\V1\TravelRequest\IndexTravelRequestAction;
use App\Actions\V1\TravelRequest\ShowTravelRequestAction;
use App\Actions\V1\TravelRequest\StoreTravelRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Dto\V1\TravelRequest\ChangeStatusTravelRequestDTO;
use App\Http\Dto\V1\TravelRequest\IndexTravelRequestDTO;
use App\Http\Dto\V1\TravelRequest\StoreTravelRequestDTO;
use App\Http\Requests\ChangeStatusTravelRequest;
use App\Http\Requests\ShowTravelRequest;
use App\Http\Requests\V1\TravelRequest\IndexTravelRequest;
use App\Http\Requests\V1\TravelRequest\StoreTravelRequest;
use App\Http\Resources\V1\TravelRequestResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TravelRequestController extends Controller
{
    public function __construct(
        private readonly StoreTravelRequestAction $storeTravelRequestAction,
        private readonly ShowTravelRequestAction $showTravelRequestAction,
        private readonly IndexTravelRequestAction $indexTravelRequestAction,
        private readonly ChangeStatusTravelRequestAction $changeStatusTravelRequestAction,
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
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden: Missing STORE_TRAVEL_REQUESTS feature'),
            new OA\Response(response: 422, description: 'Validation error'),
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
        summary: 'Show travel request details',
        security: [['bearerAuth' => []]],
        tags: ['Travel Requests'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'The unique identifier of the travel request',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new OA\JsonContent(ref: '#/components/schemas/TravelRequestResource')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden: Missing SHOW_TRAVEL_REQUESTS feature'),
            new OA\Response(response: 404, description: 'Travel request not found or does not belong to the user'),
        ]
    )]
    public function show(ShowTravelRequest $request, string $uuid)
    {
        $travelRequest = $this->showTravelRequestAction->handle($uuid);

        return response()->json(
            TravelRequestResource::make($travelRequest),
            Response::HTTP_OK
        );
    }

    #[OA\Get(
        path: '/api/v1/travel-requests',
        description: 'Returns a paginated list of travel requests belonging to the authenticated user.',
        summary: 'List travel requests with filters',
        security: [['bearerAuth' => []]],
        tags: ['Travel Requests'],
        parameters: [
            new OA\Parameter(name: 'per_page', description: 'Amount of items per page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'status', description: 'Filter by status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['requested', 'approved', 'canceled'])),
            new OA\Parameter(name: 'travelers_name', description: 'Filter by traveler name', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'destination', description: 'Filter by destination', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'departure_date', description: 'Filter by departure date (Y-m-d)', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'return_date', description: 'Filter by return date (Y-m-d)', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'created_at', description: 'Filter by creation date (Y-m-d)', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'A paginated list of travel requests',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/TravelRequestResource')),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 204, description: 'No travel requests found'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden: Missing INDEX_TRAVEL_REQUESTS feature'),
        ]
    )]
    public function index(IndexTravelRequest $request)
    {
        $dto = IndexTravelRequestDTO::fromRequest($request);
        $travelRequests = $this->indexTravelRequestAction->handle($dto);

        return response()->json(
            $travelRequests,
            $travelRequests->count() > 0 ? Response::HTTP_OK : Response::HTTP_NO_CONTENT
        );
    }

    #[OA\Patch(
        path: '/api/v1/travel-requests/{uuid}/status',
        description: 'Allows an administrator to change the status of a travel request.',
        summary: 'Update travel request status',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ChangeStatusTravelRequest')
        ),
        tags: ['Travel Requests'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'The unique identifier of the travel request',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Status updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/TravelRequestResource')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden: Missing CHANGE_STATUS_TRAVEL_REQUESTS feature'),
            new OA\Response(response: 409, description: 'Conflict: Request already approved'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function status(ChangeStatusTravelRequest $request, string $uuid)
    {
        $dto = ChangeStatusTravelRequestDTO::fromRequest($request);
        $travelRequest = $this->changeStatusTravelRequestAction->handle($dto, $uuid);

        return response()->json(TravelRequestResource::make($travelRequest), Response::HTTP_OK);
    }
}

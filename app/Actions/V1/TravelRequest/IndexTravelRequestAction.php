<?php

namespace App\Actions\V1\TravelRequest;

use App\Http\Dto\V1\TravelRequest\IndexTravelRequestDTO;
use App\Http\Resources\V1\TravelRequestResource;
use App\Repositories\V1\TravelRequest\TravelRequestRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexTravelRequestAction
{
    public function __construct(
        private TravelRequestRepositoryInterface $travelRequestRepository,
    ) {}

    public function handle(IndexTravelRequestDTO $dto): LengthAwarePaginator
    {
        $travelRequests = $this->travelRequestRepository->getAllPaginated($dto);
        $travelRequestsCollection = collect(TravelRequestResource::collection($travelRequests));

        return $travelRequests->setCollection($travelRequestsCollection);
    }
}

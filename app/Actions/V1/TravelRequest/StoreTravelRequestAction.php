<?php

namespace App\Actions\V1\TravelRequest;

use App\Http\Dto\V1\TravelRequest\StoreTravelRequestDTO;
use App\Models\TravelRequest;
use App\Repositories\V1\TravelRequest\TravelRequestRepositoryInterface;

readonly class StoreTravelRequestAction
{
    public function __construct(
        private TravelRequestRepositoryInterface $travelRequestRepository,
    ) {}

    public function handle(StoreTravelRequestDTO $dto): TravelRequest
    {
        return $this->travelRequestRepository->create($dto);
    }
}

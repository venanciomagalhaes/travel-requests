<?php

namespace App\Actions\V1\TravelRequest;

use App\Models\TravelRequest;
use App\Repositories\V1\TravelRequest\TravelRequestRepositoryInterface;

readonly class ShowTravelRequestAction
{
    public function __construct(
        private TravelRequestRepositoryInterface $travelRequestRepository,
    ) {}

    public function handle(string $uuid): TravelRequest
    {
        return $this->travelRequestRepository->findByUuid($uuid);
    }
}

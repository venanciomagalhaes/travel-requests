<?php

namespace App\Repositories\V1\TravelRequest;

use App\Http\Dto\V1\TravelRequest\StoreTravelRequestDTO;
use App\Models\TravelRequest;

interface TravelRequestRepositoryInterface
{
    public function create(StoreTravelRequestDTO $dto): TravelRequest;

    public function findByUuid(string $uuid): TravelRequest;
}

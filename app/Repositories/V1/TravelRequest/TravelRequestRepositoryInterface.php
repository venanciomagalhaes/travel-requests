<?php

namespace App\Repositories\V1\TravelRequest;

use App\Http\Dto\V1\TravelRequest\IndexTravelRequestDTO;
use App\Http\Dto\V1\TravelRequest\StoreTravelRequestDTO;
use App\Models\TravelRequest;
use Illuminate\Pagination\LengthAwarePaginator;

interface TravelRequestRepositoryInterface
{
    public function create(StoreTravelRequestDTO $dto): TravelRequest;

    public function findByUuid(string $uuid): TravelRequest;

    public function getAllPaginated(IndexTravelRequestDTO $dto): LengthAwarePaginator;
}

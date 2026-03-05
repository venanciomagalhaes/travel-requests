<?php

namespace App\Repositories\V1\TravelRequest;

use App\Http\Dto\V1\TravelRequest\StoreTravelRequestDTO;
use App\Models\TravelRequest;
use Illuminate\Support\Str;

class TravelRequestRepository implements TravelRequestRepositoryInterface
{
    public function create(StoreTravelRequestDTO $dto): TravelRequest
    {
        return TravelRequest::query()->create([
            'uuid' => Str::uuid()->toString(),
            'travelers_name' => $dto->getTravelersName(),
            'destination' => $dto->getDestination(),
            'departure_date' => $dto->getDepartureDate(),
            'return_date' => $dto->getReturnDate(),
            'status' => $dto->getStatus()->value,
            'user_id' => auth()->id(),
        ])->load('user');
    }
}

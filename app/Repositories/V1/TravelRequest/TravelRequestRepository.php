<?php

namespace App\Repositories\V1\TravelRequest;

use App\Http\Dto\V1\TravelRequest\ChangeStatusTravelRequestDTO;
use App\Http\Dto\V1\TravelRequest\IndexTravelRequestDTO;
use App\Http\Dto\V1\TravelRequest\StoreTravelRequestDTO;
use App\Models\TravelRequest;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function findByUuid(string $uuid): TravelRequest
    {
        return TravelRequest::query()->where('uuid', $uuid)->firstOrFail();
    }

    public function getAllPaginated(IndexTravelRequestDTO $dto): LengthAwarePaginator
    {
        return TravelRequest::query()
            ->when($dto->getStatus(), fn ($q, $status) => $q->where('status', $status->value))
            ->when($dto->getDestination(), fn ($q, $dest) => $q->where('destination', 'like', "%{$dest}%"))
            ->when($dto->getTravelersName(), fn ($q, $name) => $q->where('travelers_name', 'like', "%{$name}%"))
            ->when($dto->getDepartureDate(), fn ($q, $date) => $q->where('departure_date', '>=', $date))
            ->when($dto->getReturnDate(), fn ($q, $date) => $q->where('return_date', '<=', $date))
            ->when($dto->getCreatedAt(), fn ($q, $date) => $q->whereDate('created_at', $date))
            ->orderBy('created_at', 'desc')
            ->paginate($dto->getPerPage() ?? 15);
    }

    public function changeStatus(string $uuid, ChangeStatusTravelRequestDTO $dto): TravelRequest
    {
        $travelRequest = TravelRequest::query()->withoutGlobalScope('user_scope')->where('uuid', $uuid)->firstOrFail();
        $travelRequest->update(['status' => $dto->getStatus()->value]);

        return $travelRequest->load('user');
    }

    public function findByUuidWithoutUserScope(string $uuid): TravelRequest
    {
        return TravelRequest::query()->withoutGlobalScope('user_scope')->where('uuid', $uuid)->firstOrFail();
    }
}

<?php

namespace App\Actions\V1\TravelRequest;

use App\Http\Dto\V1\TravelRequest\StoreTravelRequestDTO;
use App\Models\TravelRequest;
use App\Repositories\V1\TravelRequest\TravelRequestRepositoryInterface;
use App\Services\Logger\LoggerServiceInterface;

readonly class StoreTravelRequestAction
{
    public function __construct(
        private TravelRequestRepositoryInterface $travelRequestRepository,
        private LoggerServiceInterface $logger,
    ) {}

    public function handle(StoreTravelRequestDTO $dto): TravelRequest
    {
        $travelRequest = $this->travelRequestRepository->create($dto);

        $this->logger->info('Travel request created successfully', [
            'uuid' => $travelRequest->uuid,
            'user_id' => $travelRequest->user_id,
            'destination' => $dto->getDestination(),
            'departure_date' => $dto->getDepartureDate(),
            'return_date' => $dto->getReturnDate(),
            'status' => $travelRequest->status,
            'created_at' => $travelRequest->created_at->toDateTimeString(),
        ]);

        return $travelRequest;
    }
}

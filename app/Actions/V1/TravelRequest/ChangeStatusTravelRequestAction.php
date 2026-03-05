<?php

namespace App\Actions\V1\TravelRequest;

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use App\Http\Dto\V1\TravelRequest\ChangeStatusTravelRequestDTO;
use App\Models\TravelRequest;
use App\Repositories\V1\TravelRequest\TravelRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

readonly class ChangeStatusTravelRequestAction
{
    public function __construct(
        private TravelRequestRepositoryInterface $travelRequestRepository,
    ) {}

    public function handle(ChangeStatusTravelRequestDTO $dto, string $uuid): TravelRequest
    {
        $travelRequest = $this->travelRequestRepository->findByUuidWithoutUserScope($uuid);

        if ($travelRequest->status === TravelRequestStatusEnum::APPROVED->value) {
            abort(Response::HTTP_CONFLICT, 'Travel request already approved and cannot be changed.');
        }

        if ($travelRequest->status === $dto->getStatus()->value) {
            abort(Response::HTTP_CONFLICT, "Travel request is already {$dto->getStatus()->value}.");
        }

        return $this->travelRequestRepository->changeStatus($uuid, $dto);
    }
}

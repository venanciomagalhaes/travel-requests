<?php

namespace App\Actions\V1\TravelRequest;

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use App\Http\Dto\V1\TravelRequest\ChangeStatusTravelRequestDTO;
use App\Models\TravelRequest;
use App\Notifications\V1\ChangedStatusTravelRequestNotification;
use App\Repositories\V1\TravelRequest\TravelRequestRepositoryInterface;
use App\Services\Logger\LoggerServiceInterface;
use App\Services\Notification\NotificationServiceInterface;
use Symfony\Component\HttpFoundation\Response;

readonly class ChangeStatusTravelRequestAction
{
    public function __construct(
        private TravelRequestRepositoryInterface $travelRequestRepository,
        private NotificationServiceInterface $notificationService,
        private LoggerServiceInterface $logger,
    ) {}

    public function handle(ChangeStatusTravelRequestDTO $dto, string $uuid): TravelRequest
    {
        $travelRequest = $this->travelRequestRepository->findByUuidWithoutUserScope($uuid);
        $user = $travelRequest->user;
        $newStatus = $dto->getStatus()->value;

        $this->throwAndLogIfAlreadyApproved($travelRequest, $uuid);
        $this->throwAndLogIfStatusIsSame($travelRequest, $newStatus, $uuid);

        $oldStatus = $travelRequest->status;
        $travelRequest = $this->travelRequestRepository->changeStatus($uuid, $dto);

        $this->logger->info('Travel request status updated', [
            'uuid' => $uuid,
            'user_email' => $user->email,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        $this->notificationService->send($user, new ChangedStatusTravelRequestNotification($user, $travelRequest));

        return $travelRequest;
    }

    private function throwAndLogIfAlreadyApproved(TravelRequest $travelRequest, string $uuid): void
    {
        if ($travelRequest->status === TravelRequestStatusEnum::APPROVED->value) {
            $this->logger->warning("Change status rejected: Travel request [{$uuid}] is already approved.");
            abort(Response::HTTP_CONFLICT, 'Travel request already approved and cannot be changed.');
        }
    }

    private function throwAndLogIfStatusIsSame(TravelRequest $travelRequest, string $newStatus, string $uuid): void
    {
        if ($travelRequest->status === $newStatus) {
            $this->logger->warning("Change status rejected: Request [{$uuid}] is already [{$newStatus}].");
            abort(Response::HTTP_CONFLICT, "Travel request is already {$newStatus}.");
        }
    }
}

<?php

namespace App\Http\Dto\V1\TravelRequest;

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use App\Http\Dto\V1\DtoInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

readonly class IndexTravelRequestDTO implements DtoInterface
{
    public function __construct(
        private ?int $perPage,
        private ?string $travelersName,
        private ?string $destination,
        private ?TravelRequestStatusEnum $status,
        private ?Carbon $departureDate,
        private ?Carbon $returnDate,
        private ?Carbon $createdAt,
    ) {}

    public static function fromRequest(FormRequest $request): IndexTravelRequestDTO
    {
        return new self(
            perPage: $request->validated('per_page'),
            travelersName: $request->validated('travelers_name'),
            destination: $request->validated('destination'),
            status: $request->validated('status') ? TravelRequestStatusEnum::from($request->validated('status')) : null,
            departureDate: $request->validated('departure_date') ? Carbon::parse($request->validated('departure_date')) : null,
            returnDate: $request->validated('return_date') ? Carbon::parse($request->validated('return_date')) : null,
            createdAt: $request->validated('created_at') ? Carbon::parse($request->validated('created_at')) : null,
        );
    }

    public function getTravelersName(): ?string
    {
        return $this->travelersName;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getStatus(): ?TravelRequestStatusEnum
    {
        return $this->status;
    }

    public function getDepartureDate(): ?Carbon
    {
        return $this->departureDate;
    }

    public function getReturnDate(): ?Carbon
    {
        return $this->returnDate;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function getPerPage(): ?int
    {
        return $this->perPage;
    }
}

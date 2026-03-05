<?php

namespace App\Http\Dto\V1\TravelRequest;

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use App\Http\Dto\V1\DtoInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

readonly class StoreTravelRequestDTO implements DtoInterface
{
    public function __construct(
        private string $travelersName,
        private string $destination,
        private TravelRequestStatusEnum $status,
        private Carbon $departureDate,
        private Carbon $returnDate,
    ) {}

    public static function fromRequest(FormRequest $request): StoreTravelRequestDTO
    {
        return new self(
            travelersName: $request->input('travelers_name'),
            destination: $request->input('destination'),
            status: TravelRequestStatusEnum::REQUESTED,
            departureDate: Carbon::createFromFormat('Y-m-d', $request->input('departure_date')),
            returnDate: Carbon::createFromFormat('Y-m-d', $request->input('return_date')),
        );
    }

    public function getTravelersName(): string
    {
        return $this->travelersName;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getDepartureDate(): Carbon
    {
        return $this->departureDate;
    }

    public function getReturnDate(): Carbon
    {
        return $this->returnDate;
    }

    public function getStatus(): TravelRequestStatusEnum
    {
        return $this->status;
    }
}

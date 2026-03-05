<?php

namespace App\Http\Dto\V1\TravelRequest;

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use App\Http\Dto\V1\DtoInterface;
use Illuminate\Foundation\Http\FormRequest;

readonly class ChangeStatusTravelRequestDTO implements DtoInterface
{
    public function __construct(
        private TravelRequestStatusEnum $status,
    ) {}

    public static function fromRequest(FormRequest $request): ChangeStatusTravelRequestDTO
    {
        return new self(
            status: TravelRequestStatusEnum::from($request->validated('status')),
        );
    }

    public function getStatus(): TravelRequestStatusEnum
    {
        return $this->status;
    }
}

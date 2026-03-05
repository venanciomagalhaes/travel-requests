<?php

namespace App\Enums\V1\TravelRequest;

enum TravelRequestStatusEnum: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case CANCELED = 'canceled';
}

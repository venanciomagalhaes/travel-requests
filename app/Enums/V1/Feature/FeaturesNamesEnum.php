<?php

namespace App\Enums\V1\Feature;

enum FeaturesNamesEnum: string
{
    case ME = 'me';
    case REFRESH = 'refresh';
    case LOGOUT = 'logout';
    case INDEX_TRAVEL_REQUESTS = 'index-travel-requests';
    case STORE_TRAVEL_REQUESTS = 'store-travel-requests';
    case SHOW_TRAVEL_REQUESTS = 'show-travel-requests';
    case CHANGE_STATUS_TRAVEL_REQUESTS = 'change-status-travel-requests';
    case UPDATE_TRAVEL_REQUESTS = 'update-travel-requests';
}

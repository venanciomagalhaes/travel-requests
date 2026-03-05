<?php

namespace Database\Seeders;

use App\Enums\V1\Feature\FeaturesNamesEnum;
use App\Models\Feature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'uuid' => Str::uuid()->toString(),
                'name' => FeaturesNamesEnum::ME->value,
                'description' => 'View authenticated user profile data',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => FeaturesNamesEnum::REFRESH->value,
                'description' => 'Refresh JWT authentication token',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => FeaturesNamesEnum::LOGOUT->value,
                'description' => 'Invalidate token and logout',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => FeaturesNamesEnum::INDEX_TRAVEL_REQUESTS->value,
                'description' => 'List travel requests with filters',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => FeaturesNamesEnum::STORE_TRAVEL_REQUESTS->value,
                'description' => 'Create new travel requests',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => FeaturesNamesEnum::SHOW_TRAVEL_REQUESTS->value,
                'description' => 'View specific travel request details',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => FeaturesNamesEnum::CHANGE_STATUS_TRAVEL_REQUESTS->value,
                'description' => 'Approve or cancel travel requests',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'name' => FeaturesNamesEnum::UPDATE_TRAVEL_REQUESTS->value,
                'description' => 'Edit existing travel request information',
            ],
        ];

        foreach ($features as $feature) {
            Feature::query()->firstOrCreate(
                ['name' => $feature['name']],
                ['description' => $feature['description'], 'uuid' => $feature['uuid']]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\V1\Feature\FeaturesNamesEnum;
use App\Enums\V1\Role\RolesNamesEnum;
use App\Models\Feature;
use App\Models\Role;
use App\Models\RoleFeature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::query()->where('name', RolesNamesEnum::ADMINISTRATOR->value)->firstOrFail();
        $customer = Role::query()->where('name', RolesNamesEnum::CUSTOMER->value)->firstOrFail();

        $this->attach($admin, FeaturesNamesEnum::ME);
        $this->attach($admin, FeaturesNamesEnum::REFRESH);
        $this->attach($admin, FeaturesNamesEnum::LOGOUT);
        $this->attach($admin, FeaturesNamesEnum::INDEX_TRAVEL_REQUESTS);
        $this->attach($admin, FeaturesNamesEnum::SHOW_TRAVEL_REQUESTS);
        $this->attach($admin, FeaturesNamesEnum::CHANGE_STATUS_TRAVEL_REQUESTS);

        $this->attach($customer, FeaturesNamesEnum::ME);
        $this->attach($customer, FeaturesNamesEnum::REFRESH);
        $this->attach($customer, FeaturesNamesEnum::LOGOUT);
        $this->attach($customer, FeaturesNamesEnum::INDEX_TRAVEL_REQUESTS);
        $this->attach($customer, FeaturesNamesEnum::STORE_TRAVEL_REQUESTS);
        $this->attach($customer, FeaturesNamesEnum::SHOW_TRAVEL_REQUESTS);
        $this->attach($customer, FeaturesNamesEnum::UPDATE_TRAVEL_REQUESTS);
    }

    private function attach(Role $role, FeaturesNamesEnum $featureEnum): void
    {
        $feature = Feature::query()->where('name', $featureEnum->value)->firstOrFail();

        RoleFeature::query()->firstOrCreate([
            'role_id' => $role->id,
            'feature_id' => $feature->id,
        ], [
            'uuid' => Str::uuid()->toString(),
        ]);
    }
}

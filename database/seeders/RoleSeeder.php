<?php

namespace Database\Seeders;

use App\Enums\V1\Role\RolesNamesEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RolesNamesEnum::cases() as $case) {
            Role::query()->firstOrCreate(
                ['name' => $case->value],
                ['uuid' => Str::uuid()->toString()]
            );
        }
    }
}

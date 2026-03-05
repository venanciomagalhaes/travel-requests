<?php

namespace Database\Seeders;

use App\Enums\V1\Role\RolesNamesEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@onfly.com.br'],
            [
                'uuid' => Str::uuid()->toString(),
                'role_id' => Role::query()
                    ->where('name', RolesNamesEnum::ADMINISTRATOR->value)
                    ->firstOrFail()->id,
                'name' => 'Administrator Onfly',
                'password' => Hash::make('admin@onfly.com.br'),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'customer@onfly.com.br'],
            [
                'uuid' => Str::uuid()->toString(),
                'role_id' => Role::query()
                    ->where('name', RolesNamesEnum::CUSTOMER->value)
                    ->firstOrFail()->id,
                'name' => 'Customer Onfly',
                'password' => Hash::make('customer@onfly.com.br'),
            ]
        );
    }
}

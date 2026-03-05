<?php

namespace Database\Factories;

use App\Enums\V1\TravelRequest\TravelRequestStatusEnum;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelRequestFactory extends Factory
{
    protected $model = TravelRequest::class;

    public function definition(): array
    {
        $departureDate = $this->faker->dateTimeBetween('now', '+1 month');

        return [
            'uuid' => $this->faker->uuid(),
            'travelers_name' => $this->faker->name(),
            'destination' => $this->faker->country(),
            'departure_date' => $departureDate,
            'return_date' => $this->faker->dateTimeBetween($departureDate, $departureDate->format('Y-m-d').' +15 days'),
            'status' => TravelRequestStatusEnum::REQUESTED->value,
            'user_id' => User::factory(),
        ];
    }
}

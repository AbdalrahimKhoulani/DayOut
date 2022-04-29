<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    protected $model = Trip::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'organizer_id' => 1,
            'trip_status_id' => 1,
            'description' => $this->faker->paragraph,
            'begin_date' => $this->faker->dateTime,
            'expire_date' => $this->faker->dateTime,
            'price' => $this->faker->randomNumber(),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}

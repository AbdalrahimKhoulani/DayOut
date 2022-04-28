<?php

namespace Database\Factories;

use App\Models\Place;
use App\Models\PlaceTrip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{

    protected $model = \App\Models\Place::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name,
            'address' => $this->faker->address,
            'summary' => $this->faker->paragraph,
            'description' => $this->faker->paragraph,
            'type_id' => 1,
            'created_at' => now()
        ];
    }
}

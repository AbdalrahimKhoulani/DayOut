<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    /**
     * Define the model's default s tate.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => 'ahmed',
            'last_name' => 'mohamad',
            'password' => Hash::make('123'), // password
            'photo' => $this->faker->imageUrl,
            'gender' => 'male',
            'email' => 'ahmed.mohamad@gmail.com',
            'phone_number' => '0981289706',
            'verified_at' => $this->faker->dateTime,
            'is_active' => $this->faker->boolean,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

// http://10.0.2.2:8000/

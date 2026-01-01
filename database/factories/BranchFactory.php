<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Chi nhánh ' . $this->faker->city,
            'address' => $this->faker->address,
            'phone' => $this->faker->unique()->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'map_url' => 'https://www.google.com/maps',
            'status' => $this->faker->boolean(90), // 90% là true
        ];
    }
}
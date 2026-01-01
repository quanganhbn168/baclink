<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'parent_id' => 0,
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'image' => 'uploads/categories/' . Str::random(10) . '.jpg',
            'banner' => rand(0, 1) ? 'uploads/categories/banner-' . Str::random(8) . '.jpg' : null,
            'status' => rand(0, 1),
        ];
    }
}

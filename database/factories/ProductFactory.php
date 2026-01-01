<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $price = $this->faker->numberBetween(100000, 1000000);

        return [
            'category_id' => Category::inRandomOrder()->value('id') ?? 1,
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'image' => 'uploads/products/' . Str::random(10) . '.jpg',
            'banner' => rand(0, 1) ? 'uploads/products/banner-' . Str::random(8) . '.jpg' : null,
            'price' => $price,
            'price_discount' => rand(0, 1) ? $price - rand(10000, 50000) : null,
            'description' => $this->faker->paragraphs(2, true),
            'status' => rand(0, 1),
        ];
    }
}

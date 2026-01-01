<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\PostCategory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);

        return [
            'post_category_id' => PostCategory::inRandomOrder()->value('id') ?? 1,
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'image' => 'uploads/posts/' . Str::random(10) . '.jpg',
            'banner' => rand(0, 1) ? 'uploads/posts/banner-' . Str::random(8) . '.jpg' : null,
            'description' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(5, true),
            'is_featured' => rand(0, 1),
            'status' => rand(0, 1),
        ];
    }
}

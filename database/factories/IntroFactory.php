<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intro>
 */
class IntroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Giới thiệu về chúng tôi',
            'description' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'image' => 'uploads/intros/' . uniqid() . '.jpg',
            'banner' => rand(0, 1) ? 'uploads/intros/banner-' . uniqid() . '.jpg' : null,
            'status' => true,
        ];
    }
}

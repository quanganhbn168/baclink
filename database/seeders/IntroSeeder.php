<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Intro;

class IntroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Intro::factory()->create();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
                'Răng',
                'Hàm',
                'Ca',
                'Lần',
                'Liệu trình',
                'Ngày',
                'Cái',
                'Chiếc',
            ];

            foreach ($units as $unit) {
                \App\Models\Unit::firstOrCreate([
                    'name' => $unit,
                    'slug' => \Str::slug($unit),
                ]);
            }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin mặc định
        User::create([
            'name' => 'Admin',
            'phone' => '0965625210',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'remember_token' => \Str::random(10),
        ]);

        // Thêm user ngẫu nhiên nếu muốn
        // User::factory(5)->create();
    }
}

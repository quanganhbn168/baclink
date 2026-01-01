<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
        ]);

        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'admin']);
        $admin->assignRole($role);
    }
}

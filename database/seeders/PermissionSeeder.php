<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Admin;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- QUYỀN VÀ VAI TRÒ CHO GUARD "admin" ---

        // 2. Tạo các Permissions cho trang quản trị
        $admin_permissions = [
            'view-dashboard',
            'manage-users',
            'manage-roles',
            'manage-products',
            'manage-posts',
            'manage-settings'
        ];
        foreach ($admin_permissions as $permission) {
            // Chỉ định rõ guard_name là 'admin'
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }
        echo "Admin permissions created.\n";

        // 3. Tạo vai trò Super Admin và gán quyền
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        // Ghi chú: Chúng ta sẽ dùng Gate::before để Super Admin có full quyền,
        // nên không cần gán trực tiếp ở đây.

        // Tạo vai trò khác cho admin, ví dụ: "Content Manager"
        $contentManagerRole = Role::firstOrCreate(['name' => 'Content Manager', 'guard_name' => 'admin']);
        $contentManagerRole->syncPermissions(['manage-products', 'manage-posts']);
        echo "Admin roles created and configured.\n";


        // --- QUYỀN VÀ VAI TRÒ CHO GUARD "web" ---

        // 4. Tạo các Permissions cho khách hàng
        $user_permissions = [
            'view-own-orders',
            'comment-on-products',
        ];
        foreach ($user_permissions as $permission) {
            // Chỉ định rõ guard_name là 'web'
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        echo "User permissions created.\n";

        // 5. Tạo vai trò cho khách hàng
        $customerRole = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
        $customerRole->syncPermissions(['view-own-orders', 'comment-on-products']);
        echo "User roles created and configured.\n";


        // --- TẠO USER MẪU ---

        // 6. Tạo tài khoản Super Admin (lấy từ AdminSeeder của anh)
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin123'),
            ]
        );
        $admin->assignRole($superAdminRole);
        echo "Super Admin user created.\n";

        // 7. Tạo tài khoản User/Customer mẫu (lấy từ UserSeeder của anh)
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Customer User',
                'phone' => '0123456789',
                'password' => bcrypt('user123'),
            ]
        );
        $user->assignRole($customerRole);
        echo "Customer user created.\n";
    }
}
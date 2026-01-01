<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Hiển thị trang danh sách tất cả vai trò.
     */
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->latest()->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Hiển thị form để tạo vai trò mới.
     */
    public function create()
    {
        // Lấy các quyền của guard 'admin' và nhóm chúng lại cho dễ nhìn
        $permissions = Permission::where('guard_name', 'admin')
            ->get()
            ->groupBy(function($permission) {
                return explode('-', $permission->name)[1] ?? 'khác';
        });
            
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Lưu vai trò mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,guard_name,admin',
            'permissions' => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated) {
            // Tạo vai trò với guard_name là 'admin'
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'admin'
            ]);

            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }
        });

        return redirect()->route('admin.roles.index')->with('success', 'Tạo vai trò thành công!');
    }

    /**
     * Hiển thị form để chỉnh sửa một vai trò.
     */
    public function edit(Role $role)
    {
        // Bảo vệ, không cho chỉnh sửa vai trò của guard khác
        if ($role->guard_name !== 'admin') {
            abort(403, 'Không có quyền truy cập.');
        }

        $permissions = Permission::where('guard_name', 'admin')
            ->get()
            ->groupBy(function($permission) {
                return explode('-', $permission->name)[1] ?? 'khác';
            });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Cập nhật vai trò trong database.
     */
    public function update(Request $request, Role $role)
    {
         // Bảo vệ, không cho chỉnh sửa vai trò của guard khác
        if ($role->guard_name !== 'admin') {
            abort(403, 'Không có quyền truy cập.');
        }

        if ($role->name === 'Super Admin') {
             return back()->with('error', 'Không thể chỉnh sửa vai trò Super Admin!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id . ',id,guard_name,admin',
            'permissions' => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated, $role) {
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions'] ?? []);
        });

        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật vai trò thành công!');
    }

    /**
     * Xóa vai trò khỏi database.
     */
    public function destroy(Role $role)
    {
        // Bảo vệ vai trò Super Admin không bị xóa
        if ($role->name === 'Super Admin') {
            return back()->with('error', 'Không thể xóa vai trò Super Admin!');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Xóa vai trò thành công!');
    }
}
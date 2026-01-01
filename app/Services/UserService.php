<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAllUsers(array $filters = [], int $perPage = 10)
    {
        // ... giữ nguyên code đã tạo trước đó ...
        $query = User::query()->with('roles')->latest();

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function store(array $data): User
    {
        // Vì model User đã có cast 'hashed' cho password,
        // Laravel sẽ tự động băm mật khẩu.
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user)
    {
        // Cân nhắc các ràng buộc, ví dụ user có đơn hàng thì không xóa...
        return $user->delete();
    }
}
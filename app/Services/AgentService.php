<?php

namespace App\Services;

use App\Models\User;
use App\Models\DealerTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AgentService
{
    /**
     * Lấy danh sách đại lý (Phân trang + Tìm kiếm đa trường)
     */
    public function getList(array $filters = [], int $perPage = 10)
    {
        // Chỉ lấy user ĐÃ CÓ hồ sơ đại lý
        // Eager load 'dealerProfile' để tránh lỗi N+1 Query khi hiển thị ra view
        $query = User::whereHas('dealerProfile')->with('dealerProfile');

        // Logic tìm kiếm thông minh
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            
            $query->where(function ($q) use ($keyword) {
                // 1. Tìm trong bảng Users (Tên, Email, SĐT đăng nhập)
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  
                  // 2. Tìm xuyên sang bảng DealerProfiles (Tên công ty, SĐT hotline, Mã số thuế)
                  ->orWhereHas('dealerProfile', function($qp) use ($keyword) {
                      $qp->where('company_name', 'like', "%{$keyword}%")
                         ->orWhere('phone', 'like', "%{$keyword}%") // Phone của công ty
                         ->orWhere('tax_id', 'like', "%{$keyword}%");
                  });
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Lấy chi tiết 1 đại lý (Kèm lịch sử giao dịch)
     */
    public function getDetail($id)
    {
        return User::with(['dealerProfile', 'transactions' => function($q) {
            $q->orderByDesc('created_at'); // Lịch sử mới nhất lên đầu
        }])->findOrFail($id);
    }

    /**
     * Tạo mới Đại lý (User + Profile)
     * Sử dụng Transaction để đảm bảo cả 2 cùng thành công hoặc cùng thất bại
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Tạo tài khoản đăng nhập (Users)
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'phone'    => $data['phone'], // SĐT chính chủ
                // 'is_dealer' => true, // Nếu anh dùng cột này thì bỏ comment ra
            ]);

            // 2. Tạo hồ sơ doanh nghiệp (DealerProfiles)
            $user->dealerProfile()->create([
                'company_name' => $data['company_name'] ?? null,
                'phone'        => $data['phone'], // Lưu lại phone vào profile để tiện tra cứu
                'tax_id'       => $data['tax_id'] ?? null,
                'address'      => $data['address'] ?? null,
                'facebook_id'  => $data['facebook_id'] ?? null,
                'zalo_phone'   => $data['zalo_phone'] ?? null,
                'discount_rate'=> $data['discount_rate'] ?? 0,
                'admin_note'   => $data['admin_note'] ?? null,
                'wallet_balance'=> 0,
            ]);

            return $user;
        });
    }

    /**
     * Cập nhật thông tin Đại lý
     */
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::findOrFail($id);

            // 1. Cập nhật User
            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ];

            // Nếu có nhập password mới thì mới đổi, không thì giữ nguyên
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            
            $user->update($userData);

            // 2. Cập nhật Profile (Dùng updateOrCreate để an toàn dữ liệu cũ)
            $user->dealerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $data['company_name'] ?? null,
                    'phone'        => $data['phone'],
                    'tax_id'       => $data['tax_id'] ?? null,
                    'address'      => $data['address'] ?? null,
                    'facebook_id'  => $data['facebook_id'] ?? null,
                    'zalo_phone'   => $data['zalo_phone'] ?? null,
                    'discount_rate'=> $data['discount_rate'] ?? 0,
                    'admin_note'   => $data['admin_note'] ?? null,
                ]
            );

            return $user;
        });
    }

    /**
     * Xử lý Nạp tiền (Cộng ví + Ghi log)
     */
    public function deposit($id, $amount, $note)
    {
        return DB::transaction(function () use ($id, $amount, $note) {
            $user = User::findOrFail($id);
            
            // Lấy profile (nếu chưa có thì tạo mới để tránh lỗi, dù hiếm khi xảy ra)
            $profile = $user->dealerProfile()->firstOrCreate(['user_id' => $user->id]);

            // 1. Cộng tiền
            $profile->increment('wallet_balance', $amount);

            // 2. Ghi log giao dịch
            DealerTransaction::create([
                'user_id'       => $user->id,
                'type'          => 'deposit', // Loại: Nạp tiền
                'amount'        => $amount,
                'balance_after' => $profile->wallet_balance, // Lưu số dư SAU KHI nạp để đối soát
                'note'          => $note
            ]);

            return $profile->wallet_balance;
        });
    }

    /**
     * Xóa đại lý
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        // Nhờ onDelete('cascade') trong migration, xóa User là Profile và Transaction tự mất
        return $user->delete();
    }
}
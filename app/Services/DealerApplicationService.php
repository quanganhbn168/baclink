<?php

namespace App\Services;

use App\Models\DealerApplication;
use App\Models\User;
use App\Models\DealerProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DealerApplicationService
{
    // ... (Giữ nguyên các hàm getLists, findById như cũ) ...

    public function getLists(array $filters = [], int $perPage = 10)
    {
        $query = DealerApplication::query()->orderBy('created_at', 'desc');

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('company', 'like', "%{$keyword}%");
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', (int)$filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function findById($id)
    {
        return DealerApplication::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $application = $this->findById($id);
        return $application->update($data);
    }

    public function delete($id)
    {
        $application = $this->findById($id);
        return $application->delete();
    }

    /**
     * LOGIC MỚI: Xử lý duyệt hoặc từ chối
     */
    public function updateStatus($id, int $status)
    {
        $application = $this->findById($id);

        // Trường hợp 1: DUYỆT (Status = 1) -> Tạo tài khoản Agent
        if ($status == 1) {
            // Nếu đơn này đã duyệt rồi thì thôi, không tạo lại user nữa tránh lỗi
            if ($application->status == 1) {
                return true; 
            }
            return $this->approveApplication($application);
        }

        // Trường hợp 2: TỪ CHỐI / HỦY (Status = 2) -> Chỉ đổi trạng thái
        return $application->update(['status' => $status]);
    }

    /**
     * Hàm riêng để xử lý logic Duyệt: Tạo User + Profile
     */
    protected function approveApplication($application)
    {
        // Kiểm tra xem email này đã có tài khoản chưa
        if (User::where('email', $application->email)->exists()) {
            throw new \Exception("Email {$application->email} đã tồn tại trong hệ thống User. Không thể tạo tài khoản mới.");
        }

        return DB::transaction(function () use ($application) {
            // 1. Tạo User (Mật khẩu ngẫu nhiên)
            $randomPassword = Str::random(8); // Ví dụ: aX82nMq1
            
            $user = User::create([
                'name'     => $application->name,
                'email'    => $application->email,
                'password' => Hash::make($randomPassword),
                'phone'    => $application->phone,
            ]);

            // 2. Tạo Profile Đại lý (Set cứng chiết khấu 10%)
            $user->dealerProfile()->create([
                'company_name'  => $application->company,
                'phone'         => $application->phone,
                'address'       => $application->address,
                'discount_rate' => 10, // <--- MẶC ĐỊNH 10% THEO YÊU CẦU
                'wallet_balance'=> 0,
                'admin_note'    => "Được duyệt tự động từ đơn đăng ký #{$application->id}. Password ban đầu: {$randomPassword}",
            ]);

            // 3. Cập nhật trạng thái đơn đăng ký thành Đã Duyệt
            $application->update(['status' => 1]);

            // (Optional) Return password để Controller hiển thị thông báo cho Admin
            return $randomPassword;
        });
    }
}
<?php

namespace App\Enums;
use App\Traits\EnumHelper;

enum OrderStatus: int
{
    use EnumHelper;
    
    case NEW = 0;
    case PROCESSING = 1;
    case COMPLETED = 2;
    case CANCELLED = 3;

    public function label(): string
    {
        return match($this) {
            self::NEW => 'Đơn hàng mới',
            self::PROCESSING => 'Đang xử lý',
            self::COMPLETED => 'Hoàn thành',
            self::CANCELLED => 'Đã hủy',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NEW => 'primary',    
            self::PROCESSING => 'warning', 
            self::COMPLETED => 'success', 
            self::CANCELLED => 'danger',  
        };
    }

    // Logic: Có được phép hủy không?
    public function canCancel(): bool
    {
        return match($this) {
            self::NEW, self::PROCESSING => true,
            self::COMPLETED, self::CANCELLED => false,
        };
    }

    // --- THÊM MỚI ---
    // Logic: Có được phép sửa thông tin không?
    public function canEdit(): bool
    {
        return match($this) {
            // Chỉ cho sửa khi đơn chưa hoàn thành
            self::NEW, self::PROCESSING => true,
            
            // Đã xong hoặc đã hủy thì khóa cứng dữ liệu để tránh sai lệch báo cáo/kho
            self::COMPLETED, self::CANCELLED => false,
        };
    }

    public function canDelete(): bool
    {
        return match($this) {
            // Đơn mới (spam/test) hoặc Đã hủy (rác) -> Cho phép xóa
            self::NEW, self::CANCELLED => true,
            
            // Đang xử lý hoặc Đã xong -> Giữ lại làm bằng chứng/đối soát
            self::PROCESSING, self::COMPLETED => false,
        };
    }
}
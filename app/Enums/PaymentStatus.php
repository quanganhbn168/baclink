<?php

namespace App\Enums;
use App\Traits\EnumHelper;

enum PaymentStatus: int
{
    use EnumHelper;
    
    case UNPAID = 0;
    case PAID = 1;
    case REFUNDED = 2;

    public function label(): string
    {
        return match($this) {
            self::UNPAID => 'Chưa thanh toán',
            self::PAID => 'Đã thanh toán',
            self::REFUNDED => 'Đã hoàn tiền',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::UNPAID => 'danger',    // đỏ
            self::PAID => 'success', // xanh lá
            self::REFUNDED => 'warning', // vàng
        };
    }
}
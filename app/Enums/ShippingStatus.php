<?php

namespace App\Enums;
use App\Traits\EnumHelper;

enum ShippingStatus: int
{
    use EnumHelper;
    
    case NOT_SHIPPED = 0;
    case SHIPPED = 1;
    case DELIVERED = 2;

    public function label(): string
    {
        return match($this) {
            self::NOT_SHIPPED => 'Chưa gửi hàng',
            self::SHIPPED => 'Đang giao hàng',
            self::DELIVERED => 'Giao thành công',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NOT_SHIPPED => 'primary',    // xanh dương
            self::SHIPPED => 'warning', // vàng
            self::DELIVERED => 'success', // xanh lá
        };
    }
}   
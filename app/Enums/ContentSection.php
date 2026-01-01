<?php

namespace App\Enums;

use App\Traits\EnumHelper; // Trait cũ bạn đã có

enum ContentSection: string
{
    use EnumHelper;

    case HOME_SLIDER = 'home_slider';          // Slider trang chủ
    case HOME_ACHIEVEMENT = 'home_achievement'; // Thành tựu (5+, 10k+...)
    case HOME_BENEFIT = 'home_benefit';       // Lợi ích (Giao hàng nhanh...)
    case WHY_CHOOSE_US = 'why_choose_us';     // Tại sao chọn chúng tôi
    case FOOTER_POLICY = 'footer_policy';     // Link chính sách ở Footer

    public function label(): string
    {
        return match($this) {
            self::HOME_SLIDER => 'Slider Trang chủ',
            self::HOME_ACHIEVEMENT => 'Thành tựu (Achievements)',
            self::HOME_BENEFIT => 'Lợi ích khách hàng',
            self::WHY_CHOOSE_US => 'Tại sao chọn chúng tôi',
            self::FOOTER_POLICY => 'Link Chính sách (Footer)',
        };
    }
}
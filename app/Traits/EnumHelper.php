<?php

namespace App\Traits;

trait EnumHelper
{
    /**
     * Lấy mảng [value => label] để dùng cho Select/Dropdown
     * Ví dụ: [0 => 'Đơn hàng mới', 1 => 'Đang xử lý'...]
     */
    public static function options(): array
    {
        $cases = static::cases();
        $options = [];

        foreach ($cases as $case) {
            // Kiểm tra xem Enum có hàm label() không, nếu không thì lấy tên case
            $label = method_exists($case, 'label') ? $case->label() : $case->name;
            
            // Lấy value (0, 1...) làm key, label làm value
            $options[$case->value] = $label;
        }

        return $options;
    }

    /**
     * Lấy danh sách value (keys) để dùng cho Validation
     * Ví dụ: [0, 1, 2, 3]
     */
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
    
    /**
     * Lấy mảng object đầy đủ cho API (nếu anh làm việc với JS framework như Vue/React)
     * Ví dụ: [{id: 0, name: 'Đơn hàng mới', color: 'primary'}, ...]
     */
    public static function toArray(): array
    {
        return array_map(function ($case) {
            return [
                'id' => $case->value,
                'name' => method_exists($case, 'label') ? $case->label() : $case->name,
                'color' => method_exists($case, 'color') ? $case->color() : null, // Kèm luôn màu sắc nếu cần
            ];
        }, static::cases());
    }
}
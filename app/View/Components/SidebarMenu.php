<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class SidebarMenu extends Component
{
    public array $menu;

    /**
     * Khởi tạo component.
     */
    public function __construct()
    {
        // Lấy mảng menu từ file config
        // Anh cần tạo file config/menu.php và trả về mảng menu của mình
        $this->menu = config('menu', []);
    }

    /**
     * Kiểm tra xem một item menu có đang active hay không.
     */
    public function isActive($item): bool
    {
        // Ưu tiên dùng active_pattern, nếu không có thì dùng route
        $patterns = $item['active_pattern'] ?? ($item['route'] ?? null);

        if (!$patterns) {
            return false;
        }

        // Nếu $patterns là một mảng, lặp qua và kiểm tra từng cái
        if (is_array($patterns)) {
            foreach ($patterns as $pattern) {
                if (Route::is($pattern)) {
                    return true;
                }
            }
            return false; // Không có pattern nào khớp
        }

        // Nếu là một chuỗi, kiểm tra như cũ (thêm .* để khớp cả create, edit)
        return Route::is($patterns) || Route::is($patterns . '.*');
    }

    /**
     * Kiểm tra xem một menu cha có nên được mở hay không.
     */
    public function isOpen($item): bool
    {
        // Nếu menu cha có active_pattern riêng, dùng nó để kiểm tra
        if (isset($item['active_pattern'])) {
            return $this->isActive($item);
        }

        // Nếu không, kiểm tra các submenu con của nó
        if (!empty($item['submenu'])) {
            foreach ($item['submenu'] as $sub) {
                if ($this->isActive($sub)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Lấy view / nội dung của component.
     */
    public function render()
    {
        // Trỏ đến file view của component
        // Laravel sẽ tự tìm file resources/views/components/sidebar-menu.blade.php
        return view('components.sidebar-menu');
    }
}
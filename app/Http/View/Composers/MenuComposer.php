<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Menu;

class MenuComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Danh sách các vị trí menu cần lấy
        $locations = ['top_nav', 'footer_main'];

        // Query 1 lần lấy tất cả menu cần thiết
        $menus = Menu::whereIn('location', $locations)
            ->with([
                // 1. Load items cha (Cấp 1)
                'items' => function ($query) {
                    $query->whereNull('parent_id')->orderBy('order', 'asc');
                },
                // Load quan hệ đa hình của cấp 1 + Load luôn bảng slugData để tối ưu Trait HasSlug
                'items.linkable.slugData',

                // 2. Load items con (Cấp 2 - Dropdown)
                'items.children' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                // Load quan hệ đa hình của cấp 2 + bảng slugData
                'items.children.linkable.slugData'
            ])
            ->get()
            // Chuyển Collection thành mảng Key-Value theo location để dễ gọi ở View
            // VD: $menus['top_nav']
            ->keyBy('location');

        // Truyền biến vào view
        // Anh có thể gọi $topMenu hoặc $footerMenu trực tiếp trong Blade
        $view->with('topMenu', $menus->get('top_nav'));
        $view->with('footerMenu', $menus->get('footer_main'));
    }
}
<?php

use Illuminate\Support\Facades\Route;
use App\Models\PageContent;
use Illuminate\Support\Facades\Cache;

if (!function_exists('is_active_menu')) {
    function is_active_menu($route)
    {
        return Route::is($route) || Route::is($route . '.*') ? 'active' : '';
    }
}

if (!function_exists('is_open_menu')) {
    function is_open_menu(array $submenu): string
    {
        foreach ($submenu as $item) {
            // Kiểm tra submenu lồng nhau
            if (!empty($item['submenu']) && is_open_menu($item['submenu'])) {
                return 'menu-open';
            }

            // Kiểm tra route con khớp
            if (!empty($item['route']) && Route::is($item['route'] . '*')) {
                return 'menu-open';
            }
        }

        return '';
    }
}

if (!function_exists('buildTreeOptions')) {
    /**
     * Build select options dạng phân cấp (cho danh mục)
     *
     * @param iterable $items
     * @param \Illuminate\Support\Collection $grouped (grouped by parent_id)
     * @param int|null $selected
     * @param int $depth
     * @return array
     */
    function buildTreeOptions($items, $grouped, $selected = null, $depth = 0): array
    {
        $result = [];

        foreach ($items as $item) {
            $prefix = str_repeat('— ', $depth);
            $result[$item['id']] = $prefix . $item['name'];

            if ($grouped->has($item['id'])) {
                $result += buildTreeOptions($grouped[$item['id']], $grouped, $selected, $depth + 1);
            }
        }

        return $result;
    }
}


if (!function_exists('content')) {
    /**
     * Lấy nội dung động từ bảng page_contents.
     * Tự động cache để tối ưu hiệu suất.
     */
    function content($key, $default = null)
    {
        $cacheKey = 'page_content.' . $key;

        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            $content = PageContent::where('key', $key)->first();
            return $content ? $content->value : $default;
        });
    }
}

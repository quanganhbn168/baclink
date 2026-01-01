<?php
namespace App\Observers;
use Illuminate\Support\Facades\Cache;
class MenuCacheObserver
{
    /**
     * Handle the "saved" event.
     * Sự kiện này chạy sau khi một record được TẠO MỚI hoặc CẬP NHẬT.
     */
    public function saved($model): void
    {
        Cache::forget('header_menu_structure');
        Cache::forget('footer_menu_structure');
    }
    /**
     * Handle the "deleted" event.
     * Sự kiện này chạy sau khi một record bị XÓA.
     */
    public function deleted($model): void
    {
        Cache::forget('header_menu_structure');
        Cache::forget('footer_menu_structure');
    }
}
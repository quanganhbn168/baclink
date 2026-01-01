<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use App\Observers\SettingObserver;
use Illuminate\Support\Facades\Schema;

class SettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Setting::observe(SettingObserver::class);

        try {
            // Nếu bảng chưa tồn tại thì không làm gì
            if (!Schema::hasTable('settings')) {
                return;
            }

            // Lấy hoặc tạo setting đầu tiên
            $setting = cache()->rememberForever('global_setting', function () {
                $setting = Setting::first();
                if (!$setting) {
                    $setting = Setting::create([
                        'name' => 'THT Media',
                        'logo' => 'storage/logo',
                        'favicon' => 'storage/logo',
                    ]);
                }
                return $setting;
            });

            // Share ra view
            view()->share('setting', $setting);
        } catch (\Throwable $e) {
            // Có thể log nếu cần
            // \Log::warning("SettingServiceProvider boot skipped: " . $e->getMessage());
        }
    }
}

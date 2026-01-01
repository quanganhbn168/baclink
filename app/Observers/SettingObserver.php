<?php

namespace App\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    public function saved(Setting $setting)
    {
        Cache::forget('global_setting');
    }

    public function deleted(Setting $setting)
    {
        Cache::forget('global_setting');
    }
}

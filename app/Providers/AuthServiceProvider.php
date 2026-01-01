<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Admin;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // "Bất tử hóa" Super Admin cho guard 'admin'
        Gate::before(function ($user, $ability) {
            // Kiểm tra xem user có phải là instance của model Admin không
            // và có vai trò 'Super Admin' không.
            if ($user instanceof Admin && $user->hasRole('Super Admin')) {
                return true;
            }
            return null; // Trả về null để các kiểm tra quyền khác được tiếp tục
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\CartComposer;
use App\Http\View\Composers\HeaderMenuComposer;
use App\Http\View\Composers\MenuComposer;
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.frontend.header', CartComposer::class);
        View::composer(['partials.frontend.header'],HeaderMenuComposer::class);
        View::composer(['partials.frontend.footer'],MenuComposer::class);
    }
}
<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
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
        Paginator::useBootstrapFour();
        
        // Share Header Menu with header view
        view()->composer('partials.frontend.header', function ($view) {
            $menuBuilder = app(\App\Services\MenuBuilderService::class);
            $view->with('headerMenu', $menuBuilder->getHeaderMenu());
        });

        // Share Footer Menu with footer view
        view()->composer('partials.frontend.footer', function ($view) {
             // Lấy Menu theo location footer_nav
             // Chú ý: footer.blade.php đang loop qua $footerMenu->items, nên ta truyền Instance Model Menu
             $menu = \App\Models\Menu::where('location', 'footer_nav')->with('items.linkable')->first();
             $view->with('footerMenu', $menu);
        });
    }
}

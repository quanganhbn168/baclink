<?php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
// Import Observer
use App\Observers\MenuCacheObserver;
// Import các Model
use App\Models\Category;
use App\Models\Intro;
use App\Models\ServiceCategory;
use App\Models\Product;
use App\Models\Service;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        //
    ];
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Category::observe(MenuCacheObserver::class);
        Intro::observe(MenuCacheObserver::class);
        ServiceCategory::observe(MenuCacheObserver::class);
        Product::observe(MenuCacheObserver::class);
        Service::observe(MenuCacheObserver::class);
        \App\Models\Menu::observe(MenuCacheObserver::class); // Thêm cái này
        \App\Models\MenuItem::observe(MenuCacheObserver::class); // Thêm cái này
    }
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \App\Listeners\CKFinderListener::class,
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
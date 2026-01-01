<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Services\MenuBuilderService;

class HeaderMenuComposer
{
    /**
     * @var MenuBuilderService
     */
    protected $menuBuilder;

    /**
     * Create a new profile composer.
     * Dependency Injection sẽ tự động inject Service vào đây.
     */
    public function __construct(MenuBuilderService $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with('headerMenu', $this->menuBuilder->getHeaderMenu());
    }
}
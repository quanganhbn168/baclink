<?php

if (!function_exists('get_menu_top')) {
    function get_menu_top()
    {
        return app(\App\Services\MenuBuilderService::class)->getMenuTop();
    }
}

if (!function_exists('get_menu_footer')) {
    function get_menu_footer()
    {
        return app(\App\Services\MenuBuilderService::class)->getMenuFooter();
    }
}

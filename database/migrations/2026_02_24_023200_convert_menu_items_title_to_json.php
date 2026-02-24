<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
return new class extends Migration
{
    public function up(): void
    {
        // Convert menu_items.title to JSON for translatable support
        $items = DB::table('menu_items')->get();
        
        foreach ($items as $item) {
            $title = $item->title;
            
            // Skip if already JSON
            if ($this->isJson($title)) {
                continue;
            }
            
            // Wrap existing value with default locale key
            $jsonValue = json_encode(['vi' => $title], JSON_UNESCAPED_UNICODE);
            
            DB::table('menu_items')
                ->where('id', $item->id)
                ->update(['title' => $jsonValue]);
        }

        // Clear menu cache for all locales
        Cache::forget('header_menu_structure');
        Cache::forget('footer_menu_structure');
        Cache::forget('header_menu_structure_vi');
        Cache::forget('header_menu_structure_en');
        Cache::forget('footer_menu_structure_vi');
        Cache::forget('footer_menu_structure_en');
    }

    public function down(): void
    {
        $items = DB::table('menu_items')->get();
        
        foreach ($items as $item) {
            $decoded = json_decode($item->title, true);
            if (is_array($decoded) && isset($decoded['vi'])) {
                DB::table('menu_items')
                    ->where('id', $item->id)
                    ->update(['title' => $decoded['vi']]);
            }
        }
    }

    private function isJson($string): bool
    {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE && (str_starts_with($string, '{') || str_starts_with($string, '['));
    }
};

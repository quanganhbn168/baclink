<?php

// app/Models/MenuItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslatable;

class MenuItem extends Model
{
    use HasTranslatable;

    protected $fillable = [
        'menu_id', 'parent_id', 'title', 'url', 'target', 
        'order', 'linkable_id', 'linkable_type'
    ];

    public $translatable = ['title'];

    public function linkable()
    {
        return $this->morphTo();
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Lấy Link hiển thị
     * - url = "route:home" => route('home')
     * - url = "https://..." => trả nguyên
     * - linkable => slug-based route
     */
    public function getLinkAttribute()
    {
        // 1. Route name prefix (system links)
        if (!empty($this->url) && str_starts_with($this->url, 'route:')) {
            $routeName = substr($this->url, 6);
            try {
                return route($routeName);
            } catch (\Exception $e) {
                return '#';
            }
        }

        // 2. Plain URL (custom links)
        if (!empty($this->url)) {
            return $this->url;
        }

        // 3. Morphed model (Page, Category, Intro)
        if ($this->linkable) {
            $slug = $this->linkable->slug_value;
            if ($slug) {
                return route('frontend.slug.handle', ['slug' => $slug]);
            }
        }

        // 4. Fallback
        return 'javascript:void(0);';
    }
}
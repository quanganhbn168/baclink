<?php

// app/Models/MenuItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'title', 'url', 'target', 
        'order', 'linkable_id', 'linkable_type'
    ];

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
     */
    public function getLinkAttribute()
    {
        // 1. Ưu tiên Link tĩnh (Custom URL nhập tay)
        if (!empty($this->url)) {
            return $this->url;
        }

        // 2. Nếu là Model liên kết (Page, Category...)
        if ($this->linkable) {
            // Nhờ có Trait HasSlug, ta có thể gọi slug_value
            // Nó sẽ tự tìm trong bảng slugs hoặc fallback về cột slug
            $slug = $this->linkable->slug_value;

            if ($slug) {
                // Trả về route xử lý slug chung như bạn yêu cầu
                return route('frontend.slug.handle', ['slug' => $slug]);
            }
        }

        // 3. Fallback
        return 'javascript:void(0);';
    }
}
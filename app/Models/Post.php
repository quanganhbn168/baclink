<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasImages;
use App\Traits\HasSlug;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, HasImages, HasSlug;
    protected $fillable = [
        'post_category_id',
        'title',
        'slug',
        'image',
        'banner',
        'description',
        'content',
        'is_featured',
        'status',
        'is_home',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'boolean',
        'is_home' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function getImageUrlAttribute(): string
    {
        // 1. Check MediaService relation first
        if ($this->mainImage()) {
            return $this->mainImage()->url();
        }

        // 2. Check legacy column
        if (!empty($this->image) && is_string($this->image)) {
             return asset($this->image);
        }

        return asset('images/setting/no-image.png');
    }
}

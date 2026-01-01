<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasImages;
use App\Traits\HasSlug;

class PostCategory extends Model
{
    /** @use HasFactory<\Database\Factories\PostCategoryFactory> */
    use HasFactory, HasImages, HasSlug;
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'image',
        'banner',
        'status',
        'is_home',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_home' => 'boolean',
        'parent_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name) . '-' . Str::random(5);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

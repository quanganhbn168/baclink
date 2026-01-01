<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ContentSection;

class ContentBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'section', 'title', 'subtitle', 'icon', 'image', 'description', 'url', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'section' => ContentSection::class, // Cast về Enum để dùng method label()
        'is_active' => 'boolean',
    ];

    // Helper scope để lấy data ngoài frontend cho nhanh
    public function scopeSection($query, ContentSection $section)
    {
        return $query->where('section', $section)
                     ->where('is_active', true)
                     ->orderBy('sort_order', 'asc');
    }
}
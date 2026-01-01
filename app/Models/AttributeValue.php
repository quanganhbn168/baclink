<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'color_code',
        'image',
    ];

    /**
     * Accessor để lấy URL đầy đủ của ảnh.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }
        // Bạn nên có một ảnh mặc định trong public/images
        return asset('images/setting/image_cate_3.jpg');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
        
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasImages;

class Slide extends Model
{
    use HasFactory, HasImages;

    protected $fillable = [
        'title',
        'link',
        'image',
        'type',
        'position',
        'status',
        'is_home',
    ];
    protected $casts = [
        'image' => 'json',
    ];

    // === Constants for Slide Type ===
    const TYPE_SLIDE         = 1;
    const TYPE_PARTNER  = 2;
    const TYPE_POPUP         = 3;
    const TYPE_ADVERTISEMENT = 4;

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_SLIDE         => 'Slide',
            self::TYPE_PARTNER  => 'Đối tác',
            self::TYPE_POPUP         => 'Popup',
            self::TYPE_ADVERTISEMENT => 'Quảng cáo',
        ];
    }

    public function getTypeNameAttribute(): string
    {
        return self::getTypeOptions()[$this->type] ?? 'Không xác định';
    }

    /**
     * Lấy ảnh đầy đủ path (nếu dùng asset)
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset($this->image) : asset('images/setting/no-image.png');
    }

    /**
     * Scope lọc slide đang bật
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function getBeforeImageAttribute()
    {
        $img = is_array($this->image) ? $this->image : json_decode($this->image, true);
        return $img['before'] ?? null;
    }

    public function getAfterImageAttribute()
    {
        $img = is_array($this->image) ? $this->image : json_decode($this->image, true);
        return $img['after'] ?? null;
    }

}

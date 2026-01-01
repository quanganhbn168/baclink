<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'price',
        'compare_at_price',
        'stock',
        'image',
        'is_default',
        
    ];

    // -- Accessors cho Giá --
    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_at_price > $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->getIsOnSaleAttribute()) {
            return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
        }
        return 0;
    }

    // -- Các mối quan hệ (Relationships) --
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Các giá trị thuộc tính định nghĩa nên biến thể này (VD: Đỏ, Size L).
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attribute_value');
    }
}
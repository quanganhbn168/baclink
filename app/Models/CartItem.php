<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * Các trường được phép gán hàng loạt
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    /**
     * Quan hệ: Mỗi CartItem thuộc về một User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ: Mỗi CartItem thuộc về một Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

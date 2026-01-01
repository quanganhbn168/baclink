<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class OrderDetail extends Model
{
    use HasFactory;

    // Tên bảng nếu bạn giữ là 'order_details'
    // protected $table = 'order_details'; 
    protected $table = 'order_details';
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_price',
        'quantity',
        'subtotal',
    ];
    protected $casts = [
        'product_price'       => 'decimal:2',
        'subtotal'            => 'decimal:2',
    ];
    /**
     * Lấy thông tin sản phẩm của mục này.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Lấy thông tin đơn hàng chứa mục này.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

}

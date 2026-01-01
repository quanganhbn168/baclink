<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'status',
        'payment_method',
        'note',
        
        // --- CÁC TRƯỜNG TÀI CHÍNH (BỔ SUNG) ---
        'subtotal',         // Tổng tiền hàng trước giảm
        'discount_rate',    // % Chiết khấu đại lý
        'discount_amount',  // Số tiền được giảm
        'total_price',      // Tổng thanh toán cuối cùng
        
        // --- THÔNG TIN KHÁCH HÀNG ---
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address', // Đồng bộ với Controller (lúc nãy anh dùng shipping_address)
        // 'customer_address', // Nếu trong DB anh đặt là customer_address thì sửa lại dòng trên nhé
        
        // --- TRẠNG THÁI ---
        'payment_status',
        'shipping_status',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'shipping_status' => ShippingStatus::class,
        // Cast số thực để tính toán chính xác
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Ghi đè phương thức boot của model.
     * Tự động tạo mã đơn hàng: TP-YYMMDD-XXXX
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Chỉ tạo mã nếu chưa có (đề phòng trường hợp anh tự set mã)
            if (empty($model->code)) {
                $prefix = 'EKO-' . date('ymd') . '-';
                $lastOrderToday = self::where('code', 'LIKE', $prefix . '%')->latest('id')->first();

                if ($lastOrderToday) {
                    // Lấy 4 số cuối: EKO-250811-0001 => lấy 0001
                    $lastNumber = (int) substr($lastOrderToday->code, -4);
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                $model->code = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });

        static::deleting(function ($order) {
            // Tự động xóa orderItems và order_histories khi xóa đơn hàng
            $order->orderItems()->delete();
            \Illuminate\Support\Facades\DB::table('order_histories')->where('order_id', $order->id)->delete();
        });
    }

    /**
     * Lấy thông tin khách hàng (nếu khách là thành viên).
     */
    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Lấy tất cả các chi tiết trong đơn hàng này.
     * Lưu ý: Tên model là OrderItem hay OrderDetail thì phải khớp với file model kia
     */
    public function orderItems()
    {
        // Anh kiểm tra lại xem anh đã tạo model tên là OrderItem hay OrderDetail nhé
        // Ở controller trước mình dùng OrderItem, nếu anh dùng OrderDetail thì sửa lại ở đây
        return $this->hasMany(OrderDetail::class); 
    }
}
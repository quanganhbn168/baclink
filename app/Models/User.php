<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Các đơn hàng mà người này là KHÁCH HÀNG.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Các đơn hàng mà người này được giao việc với tư cách là THỢ.
     */
    public function assignedTasks()
    {
        return $this->hasMany(Order::class, 'technician_id');
    }

    // trong app/Models/User.php
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlist()
    {
        return $this->belongsToMany(Product::class, 'wishlist');
    }

    /**
     * Quan hệ 1-1: Lấy hồ sơ đại lý của user này
     */
    public function dealerProfile()
    {
        return $this->hasOne(DealerProfile::class);
    }

    /**
     * Helper: Kiểm tra xem user này có phải đại lý không
     * (Logic: Cứ có profile thì là đại lý)
     */
    public function isDealer()
    {
        // Hoặc anh có thể dùng cột 'is_dealer' trong bảng users nếu vẫn giữ
        return $this->dealerProfile()->exists();
    }

    public function transactions()
    {
        return $this->hasMany(DealerTransaction::class, 'user_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'representative_name',
        'tax_id',
        'phone',
        'address',
        'facebook_id',
        'zalo_phone',
        'wallet_balance',
        'total_spent',
        'discount_rate',
        'admin_note'
    ];

    // Quan hệ ngược về User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
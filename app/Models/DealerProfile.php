<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasImages;

class DealerProfile extends Model
{
    use HasFactory, HasImages;

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
        'admin_note',
        'honorific',
        'position',
        'business_sector',
        'company_intro',
        'featured_products',
        'website',
        'assistant_name',
        'assistant_phone',
        'assistant_email',
    ];

    // Quan hệ ngược về User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->mainImage()) {
            return $this->mainImage()->url();
        }

        return asset('images/setting/no-image.png');
    }
}
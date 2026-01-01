<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'note'
    ];

    // Quan hệ ngược về User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
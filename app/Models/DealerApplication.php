<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerApplication extends Model
{
    protected $table = 'dealer_applications';

    protected $fillable = [
        'name','phone','email','company','address','source','message',
        'ip','user_agent','status',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    // Nếu chỉ có 1 dòng, không cần khóa chính
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'logo',
        'banner',
        'favicon',
        'email',
        'phone',
        'address',
        'bank_name',
        'bank_account_no',
        'bank_account_name',
        'bank_qr_template',
        'map',
        'schema_script',
        'meta_description',
        'meta_keywords',
        'meta_image',
        'zalo',
        'mess',
        'tiktok',
        'youtube',
        'head_script',
        'body_script',
    ];
}

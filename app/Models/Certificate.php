<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory, UploadImageTrait; 

    protected $fillable = [
        'name', 'slug', 'image', 'issued_by',
        'issued_at', 'expired_at', 'description', 'status',
    ];
}

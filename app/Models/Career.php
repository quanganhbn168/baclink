<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'quantity',
        'salary',
        'experience',
        'deadline',
        'description',
        'requirements',
        'benefits',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'deadline' => 'date',
    ];
}
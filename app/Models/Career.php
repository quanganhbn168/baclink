<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslatable;

class Career extends Model
{
    use HasFactory, HasTranslatable;

    public $translatable = ['name', 'description', 'requirements', 'benefits'];

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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;
use App\Traits\HasTranslatable;
class Testimonial extends Model
{
    /** @use HasFactory<\Database\Factories\TestimonialFactory> */
    use HasFactory, HasImages, HasTranslatable;

    public $translatable = ['name', 'content'];
    protected $fillable = [
        'name',
        'position',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}

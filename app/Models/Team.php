<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslatable;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory, HasTranslatable;

    public $translatable = ['name', 'position', 'bio'];
    protected $fillable = [
        'name',
        'position',
        'image',
        'hsk_level',
        'experience',
        'bio',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasImages;
use App\Traits\HasTranslatable;
class Brand extends Model
{
    use HasFactory, HasImages, HasTranslatable;

    public $translatable = ['name'];

    protected $fillable = ['name', 'slug', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class); 
    }
}

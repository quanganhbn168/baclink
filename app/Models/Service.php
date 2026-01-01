<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImageGallery;
use App\Traits\UploadImageTrait;

class Service extends Model
{
    use HasFactory, HasImageGallery, UploadImageTrait;

    protected $fillable = [
        'service_category_id',
        'name',
        'slug',
        'image',
        'banner',
        'description',
        'content',
        'is_home',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class,"service_category_id");
    }
    public function slug()
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    public function getSlugUrlAttribute()
    {
        return url($this->slug->slug ?? '#');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'item');
    }

}

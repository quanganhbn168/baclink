<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slug extends Model
{
    protected $fillable = ['slug'];

    public function sluggable()
    {
        return $this->morphTo();
    }
}

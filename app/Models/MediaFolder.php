<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MediaFile;

class MediaFolder extends Model
{
    protected $fillable = ['name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(MediaFile::class, 'folder_id');
    }
}

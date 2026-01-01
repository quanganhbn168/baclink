<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MediaFolder;

class MediaFile extends Model
{
    protected $fillable = [
        'name', 
        'path', 
        'disk', 
        'mime_type', 
        'size', 
        'folder_id'
    ];

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }
}

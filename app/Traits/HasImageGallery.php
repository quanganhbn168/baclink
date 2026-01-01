<?php


namespace App\Traits;

use App\Models\Image;

trait HasImageGallery
{
    public function images()
    {
        return $this->hasMany(Image::class, 'item_id')
            ->where('item_type', static::class);
    }

    public function addImage(string $imagePath): void
    {
        Image::create([
            'item_type' => static::class,
            'item_id' => $this->id,
            'image' => $imagePath,
        ]);
    }

    public function clearImages(): void
    {
        Image::where('item_type', static::class)
            ->where('item_id', $this->id)
            ->delete();
    }
}

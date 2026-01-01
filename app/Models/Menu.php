<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'location'];

    // Lấy danh sách items (chỉ lấy cấp cha cao nhất để đệ quy ở View)
    public function items()
    {
        return $this->hasMany(MenuItem::class)
                    ->whereNull('parent_id')
                    ->orderBy('order');
    }
}
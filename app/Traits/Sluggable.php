<?php

namespace App\Traits;

use App\Models\Slug;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait Sluggable
{
    /**
     * Tự động đăng ký các sự kiện của Eloquent.
     */
    protected static function bootSluggable()
    {
        // Khi một model MỚI được tạo
        static::created(function (Model $model) {
            $model->generateSlug();
        });

        // Khi một model được CẬP NHẬT
        static::updated(function (Model $model) {
            $model->generateSlug();
        });

        // Khi một model bị XÓA
        static::deleted(function (Model $model) {
            $model->slug()->delete();
        });
    }

    /**
     * Quan hệ đa hình đến bảng Slugs.
     */
    public function slug()
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    /**
     * Phương thức chính để tạo hoặc cập nhật slug.
     */
    protected function generateSlug()
    {
        // Lấy tiêu đề từ cột 'title' hoặc 'name' của model
        $from = $this->name ?? $this->title ?? '';

        // Tạo slug từ tiêu đề
        $newSlug = Str::slug($from);

        // Lưu slug vào bảng slugs
        $this->slug()->updateOrCreate(
            // Điều kiện tìm kiếm (không cần, vì đã có quan hệ)
            [], 
            // Dữ liệu để tạo hoặc cập nhật
            ['slug' => $newSlug]
        );
    }
}
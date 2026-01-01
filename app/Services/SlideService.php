<?php

namespace App\Services;

use App\Contracts\MediaServiceContract;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SlideService
{
    /** Cấu hình xử lý ảnh slide 1920×600 */
    private const IMAGE_CONFIG = [
        'main'     => ['width' => 1920, 'height' => 600, 'fit' => true],
        'variants' => [
            // Thumb gọn để hiển thị danh sách
            'thumbnail' => ['width' => 300, 'height' => 94, 'fit' => true],
        ],
        'quality'  => 85,
        'format'   => 'webp',
    ];

    public function __construct(
        protected MediaServiceContract $mediaService
    ) {}

    /**
     * Danh sách slide + filter cơ bản (keyword, status) + phân trang.
     * Trả về mảng 1 phần tử để khớp Controller: [$slides]
     */
    public function list(Request $request): array
    {
        $perPage = (int) $request->integer('per_page', 20);

        $slides = Slide::query()
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $kw = trim((string) $request->get('keyword'));
                $q->where(function ($qq) use ($kw) {
                    $qq->where('title', 'LIKE', "%{$kw}%")
                       ->orWhere('link',  'LIKE', "%{$kw}%");
                });
            })
            ->when($request->filled('status') || $request->get('status') === '0', function ($q) use ($request) {
                $q->where('status', (int) $request->get('status'));
            })
            ->orderBy('position')
            ->orderByDesc('id')
            ->paginate($perPage);

        return [$slides];
    }

    /**
     * Tạo slide mới.
     * $data nhận các field thường + 'image_original_path' (từ media-input).
     */
    public function create(array $data): Slide
    {
        $slideData = Arr::except($data, ['image_original_path']);

        $slide = Slide::create($slideData);

        // Xử lý ảnh đại diện (nếu có)
        $this->mediaService->updateMedia(
            $slide,
            $data['image_original_path'] ?? null,
            'slides',                         // thư mục đích
            self::IMAGE_CONFIG,
            fn ($imgData) => $slide->setMainImage($imgData), // setter từ HasImages
            null,
            'Ảnh slide'
        );

        return $slide;
    }

    /**
     * Cập nhật slide.
     * Không thay ảnh nếu không truyền image_original_path.
     */
    public function update(Slide $slide, array $data): Slide
    {
        $slideData = Arr::except($data, ['image_original_path']);

        $slide->update($slideData);

        // Cập nhật ảnh nếu có path mới
        $this->mediaService->updateMedia(
            $slide,
            $data['image_original_path'] ?? null,
            'slides',
            self::IMAGE_CONFIG,
            fn ($imgData) => $slide->setMainImage($imgData),
            fn () => $slide->mainImage(), // trả về Image hiện tại để service biết xoá/thay
            'Ảnh slide'
        );

        return $slide;
    }

    /**
     * Xoá slide + toàn bộ ảnh liên quan.
     */
    public function delete(Slide $slide): void
    {
        // Xoá file vật lý của mọi ảnh liên kết
        foreach ($slide->images as $image) {
            $this->mediaService->deleteProcessedImages($image);
        }

        $slide->images()->delete();
        $slide->delete();
    }
}

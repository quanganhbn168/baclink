<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadVideoTrait
{
    public function uploadVideo(UploadedFile $file, string $folder = 'uploads/videos'): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext !== 'mp4') {
            abort(422, 'Chỉ chấp nhận file MP4.');
        }

        if ($file->getSize() > 100 * 1024 * 1024) {
            abort(422, 'Dung lượng video quá lớn.');
        }

        $filename = uniqid() . '-' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $ext;
        $path = $file->storeAs($folder, $filename, 'public');

        return 'storage/' . $path;
    }

    public function deleteVideo(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/')) {
            $path = str_replace('storage/', '', $path);
            Storage::disk('public')->delete($path);
        }
    }
}

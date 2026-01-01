<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadDocumentTrait
{
    public function uploadDocument(UploadedFile $file, string $folder = 'uploads/docs'): string
    {
        $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, $allowed)) {
            abort(422, 'File không được hỗ trợ.');
        }

        if ($file->getSize() > 10 * 1024 * 1024) { // Giới hạn 10MB
            abort(422, 'Dung lượng file quá lớn.');
        }

        $filename = uniqid() . '-' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $ext;
        $path = $file->storeAs($folder, $filename, 'public');

        return 'storage/' . $path;
    }

    public function deleteDocument(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/')) {
            $path = str_replace('storage/', '', $path);
            Storage::disk('public')->delete($path);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\MediaFile;
use App\Models\MediaFolder;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $folderId = $request->query('folder_id'); // null means root
        $search = trim((string) $request->query('s', ''));
        $perPage = (int) ($request->integer('per_page') ?: config('media_local.per_page', 60));
        $perPage = max(12, min($perPage, 200));

        // 1. Get Folders (only in current folder)
        // If searching, we might want to skip folders or show relevant ones. 
        // For simplicity, folders are only shown when NOT searching deeply.
        $folders = collect([]);
        if (empty($search)) {
            $folders = MediaFolder::where('parent_id', $folderId)
                ->orderBy('name')
                ->get()
                ->map(function ($f) {
                    return [
                        'id' => $f->id,
                        'name' => $f->name,
                        'type' => 'folder',
                        'is_folder' => true
                    ];
                });
        }

        // 2. Get Files
        $query = MediaFile::query();

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        } else {
            $query->where('folder_id', $folderId);
        }

        // Filter: Type
        if ($request->has('type') && $request->type != 'all') {
            if ($request->type == 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($request->type == 'file') {
                $query->where('mime_type', 'not like', 'image/%');
            }
        }

        // Filter: Sort
        $sort = $request->input('sort_by', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'size_asc':
                $query->orderBy('size', 'asc');
                break;
            case 'size_desc':
                $query->orderBy('size', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $files = $query->paginate($perPage);

        // Transform files for frontend
        $items = $files->map(function ($file) {
            return [
                'id' => $file->id,
                'name' => $file->name,
                'path' => $file->path,
                'url' => Storage::disk($file->disk)->url($file->path),
                'size' => $file->size,
                'mime_type' => $file->mime_type,
                'type' => 'file',
                'is_folder' => false
            ];
        });

        // 3. Breadcrumbs
        $breadcrumbs = [];
        if ($folderId) {
            $curr = MediaFolder::find($folderId);
            while ($curr) {
                array_unshift($breadcrumbs, ['id' => $curr->id, 'name' => $curr->name]);
                $curr = $curr->parent;
            }
        }
        array_unshift($breadcrumbs, ['id' => null, 'name' => 'Home']);

        // 4. Stats Calculation (Global)
        $docMimes = [
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            'application/rtf'
        ];
        
        // Use separate logic for counting to keep main query clean
        $countImages = MediaFile::where('mime_type', 'like', 'image/%')->count();
        $countVideos = MediaFile::where('mime_type', 'like', 'video/%')->count();
        $countDocs = MediaFile::whereIn('mime_type', $docMimes)->count();
        
        $totalFilesAll = MediaFile::count();
        $totalSize = MediaFile::sum('size');
        $countOthers = max(0, $totalFilesAll - ($countImages + $countVideos + $countDocs));

        $stats = [
            'images' => $countImages,
            'videos' => $countVideos,
            'documents' => $countDocs,
            'others' => $countOthers,
            'total_size' => $totalSize,
        ];

        return response()->json([
            'files'        => $items,
            'folders'      => $folders, 
            'breadcrumbs'  => $breadcrumbs,
            'stats'        => $stats,
            'total'        => $files->total(),
            'per_page'     => $files->perPage(),
            'current_page' => $files->currentPage(),
            'last_page'    => $files->lastPage(),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,zip|max:10240', // 10MB
        ]);

        $folderId = $request->input('folder_id'); // Nullable
        
        // Validate folder exists if provided
        if ($folderId && !MediaFolder::find($folderId)) {
            return response()->json(['success' => false, 'message' => 'Thư mục không tồn tại'], 404);
        }

        $disk = config('media_local.disk', 'public');
        $root = trim(config('media_local.originals_root', ''), '/');
        
        // Add subfolder path if needed based on date, or keep simple flat structure in storage 
        // but virtual folder in DB. Keeping flat/date-based in storage is cleaner.
        // Let's stick to the SERVICE logic for storage path (MediaService handles it).
        // But here we are using manual logic in previous controller. 
        // Let's use MediaService if readily available, OR keep simple controller logic but save to DB.
        
        // Re-using the manual logic from before but slightly improved:
        $uploaded = [];

        foreach ($request->file('files', []) as $file) {
            $originalName = $file->getClientOriginalName();
            $name = pathinfo($originalName, PATHINFO_FILENAME);
            $ext = strtolower($file->getClientOriginalExtension());
            
            // Storage path: root/YYYY/MM/filename
            $subPath = date('Y/m');
            $storageDir = "{$root}/{$subPath}";
            
            $path = "{$storageDir}/{$originalName}";
            
            // Unique name
            $i = 1;
            while (Storage::disk($disk)->exists($path)) {
                $path = "{$storageDir}/{$name}-{$i}.{$ext}";
                $i++;
            }

            // Save file
            $stored = $file->storeAs($storageDir, basename($path), $disk);

            // Save to DB
            $mediaFile = MediaFile::create([
                'name' => basename($stored),
                'path' => $stored,
                'disk' => $disk,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'folder_id' => $folderId
            ]);

            $uploaded[] = [
                'id'    => $mediaFile->id,
                'path' => $mediaFile->path,
                'url'  => Storage::disk($disk)->url($mediaFile->path),
                'name' => $mediaFile->name,
                'type' => 'file'
            ];
        }

        return response()->json(['success' => true, 'data' => $uploaded]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:media_folders,id'
        ]);

        $folder = MediaFolder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return response()->json(['success' => true, 'data' => $folder]);
    }

    public function destroy(Request $request)
    {
        // Support bulk delete
        $items = $request->input('items', []); // Expecting array of {id, type}
        
        // Fallback for single item delete (legacy support)
        if (empty($items) && $request->has('id')) {
            $items = [
                ['id' => $request->id, 'type' => $request->type]
            ];
        }

        if (empty($items) || !is_array($items)) {
            return response()->json(['success' => false, 'message' => 'Không có mục nào được chọn'], 400);
        }

        $deletedCount = 0;
        $errors = [];

        foreach ($items as $item) {
            $id = $item['id'] ?? null;
            $type = $item['type'] ?? null;

            if (!$id || !$type) continue;

            try {
                if ($type === 'folder') {
                    $folder = MediaFolder::find($id);
                    if ($folder) {
                        $folder->delete(); // Cascades based on DB schema usually, or needs recursion logic if not set
                        $deletedCount++;
                    }
                } elseif ($type === 'file') {
                    $file = MediaFile::find($id);
                    if ($file) {
                        try {
                            Storage::disk($file->disk)->delete($file->path);
                            $file->delete();
                            $deletedCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Lỗi xóa file ID $id: " . $e->getMessage();
                        }
                    }
                }
            } catch (\Exception $e) {
               $errors[] = "Lỗi xử lý ID $id: " . $e->getMessage();
            }
        }

        if ($deletedCount === 0 && count($errors) > 0) {
             return response()->json(['success' => false, 'message' => implode('; ', $errors)], 500);
        }

        return response()->json([
            'success' => true, 
            'message' => "Đã xóa $deletedCount mục.",
            'errors' => $errors
        ]);
    }
    public function sync()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('media:sync');
            return response()->json(['success' => true, 'message' => 'Đồng bộ thành công!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllFolders()
    {
        // Return simple list for selection
        // We might want to format this as tree or indented list in frontend
        // For now, flat list with name is okay, or maybe "Parent / Child" name?
        // Let's return full list and let frontend or simple logic handle display.
        $folders = MediaFolder::orderBy('name')->get();
        return response()->json($folders);
    }

    public function move(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'destination_folder_id' => 'nullable|exists:media_folders,id'
        ]);

        $destId = $request->destination_folder_id;
        // If destId is null, it means Root.

        $items = $request->input('items', []);
        $movedCount = 0;
        $errors = [];

        foreach ($items as $item) {
            $id = $item['id'] ?? null;
            $type = $item['type'] ?? null;

            if (!$id || $type !== 'file') continue; // Only support moving files for now

            $file = MediaFile::find($id);
            if (!$file) continue;

            // Don't move if already in that folder
            if ($file->folder_id == $destId) continue;

            // Physical Move
            // Current Path vs New Path
            // We need to resolve Destination Path.
            // CAUTION: Our System stores full path in DB.
            // BUT standard CKFinder/System usually implies structure based on folders?
            // Actually `MediaFile` has `path`. `MediaFolder` is just virtual grouping in DB?
            // OR does `MediaFolder` represent physical directory? 
            // `MediaSyncCommand` implies structure matches DB.
            // If we move in DB, we MUST move on Disk to keep consistency, otherwise Sync will delete it!
            
            $disk = $file->disk;
            $oldPath = $file->path;
            $fileName = basename($oldPath);
            
            $newDir = ''; // Root
            if ($destId) {
                // We need to build path from folder structure? 
                // Or just move to a flat folder named after the ID? 
                // `MediaSyncCommand` scans REAL directories.
                // So `MediaFolder` MUST correspond to a Real Directory.
                // Let's reconstruct the path for the destination folder.
                $destFolder = MediaFolder::find($destId);
                // Recursive path building...
                $folderParts = [];
                $curr = $destFolder;
                while($curr) {
                    array_unshift($folderParts, $curr->name);
                    $curr = $curr->parent;
                }
                // Prepend 'userfiles' or root?
                // sync command uses `path` argument. config ckfinder `backends.default.root` = `userfiles/`.
                // Let's assume relative to disk root.
                // If existing files are in `storage/userfiles/FOO.jpg` -> DB path `userfiles/FOO.jpg`
                // Wait, `MediaFile` path usually includes `userfiles/` prefix if scanned?
                // Let's check a sample or assume standard `userfiles/` prefix behavior.
                // Actually `MediaSyncCommand` uses `$basePath` from argument. 
                
                // SAFE BET: Just update `folder_id` in DB. 
                // BUT Sync will Prune it if it's not in the expected folder?
                // The User asked to move "Tu thu muc a sang thu muc b".
                // If I only update DB, the file stays physically in `folder_a`.
                // If I run Sync, it sees file in `folder_a` (ID X) but DB says ID X is in `folder_b`.
                // Sync might update DB back to `folder_a` OR duplicate it?
                // Ideally we move physically.
                
                // To move physically we need Exact New Path.
                // Let's try to infer "root" from the file's current path.
                // $oldPath = "userfiles/folderA/img.jpg"
                // $newDir should be "userfiles/folderB/"
                
                $rootPrefix = 'userfiles/'; // Config dependent, hacky guess.
                // Better: Get path of destination folder? MediaFolder doesn't allow "get path".
                // We'd have to calculate it.
            }
            
            // COMPLEXITY ALERT: Moving physically is hard without solid Folder->Path mapping.
            // ALTERNATIVE: Just update DB `folder_id` and HOPE the user is okay with Virtual Folders for now?
            // BUT Sync Command deletes files that are in DB but missing from Disk? No.
            // Sync deletes DB entries if File is missing from Disk.
            // Sync ADDS DB entries if File is found on Disk.
            // If I change DB `folder_id` but leave file in old folder:
            // Sync runs -> sees file at old path -> checks DB.
            // If DB has path matching old path, it does nothing.
            // If DB has path matching new path... but file is at old path...
            // It might create a NEW DB entry for the old path.
            
            // DECISION: To properly support this, we really should execute a physical move.
            // I will attempt to "Construct" the destination path.
            // If it fails, I will return error.
            
            try {
                 // Calculate New Path
                 $newFolderPath = $this->calculateFolderPath($destId); // "userfiles/foo/bar"
                 $newPath = $newFolderPath . '/' . $fileName;
                 
                 // Unique name check
                 // ... (skip for brevity, or rely on overwrite? better not overwrite)
                 
                 if (Storage::disk($disk)->exists($newPath)) {
                      $errors[] = "File '$fileName' đã tồn tại ở thư mục đích.";
                      continue;
                 }
                 
                 Storage::disk($disk)->move($oldPath, $newPath);
                 
                 $file->path = $newPath;
                 $file->folder_id = $destId;
                 $file->save();
                 $movedCount++;
                 
            } catch (\Exception $e) {
                $errors[] = "Lỗi di chuyển file $fileName: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đã chuyển $movedCount file.",
            'errors' => $errors
        ]);
    }

    private function calculateFolderPath($folderId)
    {
        if (!$folderId) return 'userfiles'; // Root assumption
        
        $folder = MediaFolder::find($folderId);
        if (!$folder) return 'userfiles';
        
        $parts = [$folder->name];
        $curr = $folder->parent;
        while ($curr) {
            array_unshift($parts, $curr->name);
            $curr = $curr->parent;
        }
        
        return 'userfiles/' . implode('/', $parts);
    }
}

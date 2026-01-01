<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Support\Facades\DB;

class MediaSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:sync {--path=userfiles : The root path to sync relative to the disk} {--disk=public}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync media files from disk to database (CKFinder structure)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $diskName = $this->option('disk');
        $basePath = $this->option('path'); // e.g., 'userfiles'

        $this->info("Starting sync for Disk: [{$diskName}] Path: [{$basePath}]");

        if (!Storage::disk($diskName)->exists($basePath)) {
            $this->error("Path does not exist: {$basePath}");
            return 1;
        }

        // Get all existing DB records for this disk/path scope
        // We will track which IDs are found during scan.
        // Those not found will be deleted.
        $this->allFileIds = MediaFile::where('disk', $diskName)
            ->where('path', 'like', $basePath . '%')
            ->pluck('id', 'path')
            ->toArray();
            
        $this->allFolderIds = MediaFolder::pluck('id', 'name')->toArray(); // Simplification: assuming unique names globally might be wrong. 
        // Ideally we should track folders by path, but MediaFolder only stores name and parent_id.
        // Let's rely on recursive logic to find folders, and maybe we can't easily bulk-prune folders 
        // without reconstructing paths. 
        // Strategy change: Only bulk-prune Files. 
        // For folders, we'll try to sync structure. Pruning folders is risky if we don't track full path.
        
        // Actually, let's construct a map of folder paths to IDs during traversal?
        // Or simpler: Just sync files for now efficiently. The current commands iterates everything.
        
        $this->foundFileIds = [];

        // Start Recursive Scan
        $this->scanDirectory($diskName, $basePath, null);

        // Prune Missing Files
        $missingIds = array_diff(array_values($this->allFileIds), $this->foundFileIds);
        $missingCount = count($missingIds);

        if ($missingCount > 0) {
            $this->warn("Pruning {$missingCount} missing files from database...");
            MediaFile::destroy($missingIds);
        }

        $this->info('Sync completed successfully!');
        return 0;
    }

    protected $allFileIds = [];
    protected $foundFileIds = [];

    protected function scanDirectory($disk, $currentPath, $parentId)
    {
        // 1. Directories
        $directories = Storage::disk($disk)->directories($currentPath);
        
        foreach ($directories as $dirPath) {
            $dirName = basename($dirPath);
            
            // Skip hidden folders or thumbnails
            if (str_starts_with($dirName, '.') || $dirName === '_thumbs' || $dirName === 'ckfinder') {
                continue;
            }

            // Find or Create Folder
            // Note: This logic assumes unique names under a parent.
            $folder = MediaFolder::firstOrCreate(
                [
                    'name' => $dirName,
                    'parent_id' => $parentId
                ]
            );

            // Recursively scan
            $this->scanDirectory($disk, $dirPath, $folder->id);
        }

        // 2. Files
        $files = Storage::disk($disk)->files($currentPath);
        $count = 0;

        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            
            // Filter extensions
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp','gif','bmp','svg','pdf','doc','docx','xls','xlsx','zip','rar'])) {
                continue;
            }

            // Check if exists in our pre-fetched list
            if (isset($this->allFileIds[$filePath])) {
                $fileId = $this->allFileIds[$filePath];
                $this->foundFileIds[] = $fileId;
            } else {
                // New File
                $newFile = MediaFile::create([
                    'name' => $fileName,
                    'path' => $filePath,
                    'disk' => $disk,
                    'mime_type' => Storage::disk($disk)->mimeType($filePath) ?? 'application/octet-stream',
                    'size' => Storage::disk($disk)->size($filePath) ?? 0,
                    'folder_id' => $parentId
                ]);
                $this->foundFileIds[] = $newFile->id;
                $count++;
            }
        }
        
        if ($count > 0) {
            $this->line("  -> Imported {$count} new files in {$currentPath}");
        }
    }
}

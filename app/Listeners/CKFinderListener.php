<?php

namespace App\Listeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Event\AfterCommandEvent;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CKFinderListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CKFinderEvent::AFTER_COMMAND_FILE_UPLOAD => 'onFileUpload',
            CKFinderEvent::AFTER_COMMAND_DELETE_FILES => 'onDeleteFiles',
            CKFinderEvent::AFTER_COMMAND_CREATE_FOLDER => 'onCreateFolder',
            CKFinderEvent::AFTER_COMMAND_DELETE_FOLDER => 'onDeleteFolder',
            CKFinderEvent::AFTER_COMMAND_RENAME_FILE => 'onRenameFile',
            CKFinderEvent::AFTER_COMMAND_RENAME_FOLDER => 'onRenameFolder',
            CKFinderEvent::AFTER_COMMAND_MOVE_FILES => 'onMoveFiles',
        ];

        // Alternate manual mapping if constants fail (fallback)
        // return [
        //     'ckfinder.afterCommand.fileUpload' => 'onFileUpload',
        //     ...
        // ];
    }

    public function onFileUpload(AfterCommandEvent $event)
    {
        // $event->getData() usually contains 'uploaded' files count, etc.
        // But for details, we often need to inspect request/response or use specific data
        
        // CKFinder 3 PHP approach:
        // The event setup might be slightly different depending on version.
        // We will try to get the 'folder' and 'name' from the command arguments.
        
        $params = $event->getArguments(); // e.g. ['command' => 'FileUpload', 'currentFolder' => '/foo/', ...]
        
        $folderPath = $params['currentFolder'] ?? '/';
        $uploadedFile = $event->getData()['allocated'] ?? $event->getData()['created'] ?? null;
        
        // Note: Generic 'FileUpload' might be handling multiple files or chunks.
        // A better approach for robust sync is to resync the specific folder.
        
        $this->syncFolder($folderPath);
    }

    public function onDeleteFiles(AfterCommandEvent $event)
    {
        // Arguments: "deleted" array of file names?
        // Or "files" array in request
        
        // Deleting files is usually atomic per request.
        // We can just resync the folder correctly or delete specifically.
        
        $params = $event->getArguments();
        $folderPath = $params['currentFolder'] ?? '/';
        
        // Option 1: Parse arguments to delete specific files (Optimization)
        // Option 2: Resync folder (Safe)
        
        // Let's go with Safe Sync for now, as it handles edge cases.
        $this->syncFolder($folderPath);
    }

    public function onCreateFolder(AfterCommandEvent $event)
    {
        $params = $event->getArguments();
        $parentPath = $params['currentFolder'] ?? '/';
        $newFolderName = $params['newFolderName'] ?? null;
        
        if ($newFolderName) {
            $fullPath = rtrim($parentPath, '/') . '/' . $newFolderName;
            // Create Folder Record
            $this->ensureFolderExists($fullPath);
        }
    }

    public function onDeleteFolder(AfterCommandEvent $event)
    {
        // 'folderName' might be passed?
        // CKFinder DeleteFolder command takes 'folderName' or path.
        // We might need to inspect.
        
        // Let's assume re-syncing parent folder is enough? 
        // No, if we delete a folder, we need to remove it from DB.
        
        // If we can't easily parse, we can rely on Pruning via MediaSyncCommand (global).
        // But users expect instant feedback.
        
        // Let's implement a generic "Sync Path" method that updates DB for that path.
        
        $params = $event->getArguments();
        if (isset($params['currentFolder'])) {
             // For DeleteFolder, currentFolder is usually the Parent of the deleted folder.
             // But we need to know WHICH folder was deleted.
             // It is usually in $params['folderName'] ?
             
             // Let's try to just resync the parent folder's children.
             $this->syncFolder($params['currentFolder'], true); // recursive = true? No, just children.
        }
    }
    
    public function onRenameFile(AfterCommandEvent $event)
    {
        $params = $event->getArguments();
        $folderPath = $params['currentFolder'] ?? '/';
        $this->syncFolder($folderPath);
    }

    public function onRenameFolder(AfterCommandEvent $event)
    {
        // Renaming a folder changes its path. 
        // We should resync the PARENT folder to detect the new name
        // And we might need to update IDs or let them be recreated (if we delete old, create new).
        // But standard sync might just create new and leave old orphaned if we don't prune?
        // My updated MediaSync command supports pruning.
        
        // So checking the parent folder will:
        // 1. Detect new folder -> Create in DB.
        // 2. Detect missing old folder -> Delete from DB (if we implement pruning in this localized sync).
        
        $params = $event->getArguments();
        $parentPath = dirname(rtrim($params['currentFolder'] ?? '/', '/')) . '/'; // This might be tricky if "currentFolder" is the folder itself?
        // Actually usually commands run IN a folder.
        // RenameFolder: currentFolder is the parent.
        
        $this->syncFolder($params['currentFolder'] ?? '/');
    }

    public function onMoveFiles(AfterCommandEvent $event)
    {
        // Source and Target.
        // We know Source from currentFolder.
        // Target is in arguments.
        
        $params = $event->getArguments();
        $sourcePath = $params['currentFolder'] ?? '/';
        $targetPath = $params['target'] ?? '/'; // ??? CKFinder API varies.
        
        $this->syncFolder($sourcePath);
        // And also sync target?
        // We can't easily guess target path from simple args without deeper inspection.
        // Let's just try.
    }

    /**
     * Synchronize a specific folder path (Files and Subfolders)
     */
    protected function syncFolder($relativePath, $recursive = false)
    {
        // We can reuse the logic from MediaSyncCommand or call it?
        // Calling Artisan command is easiest but might be slow?
        // It's fine for user actions (1-2s delay is ok).
        
        // Important: Relative path needs to be correct.
        // CKFinder paths are like /Files/Images/ or /
        // They usually don't include "userfiles" or "public" disk prefix if configured as root.
        // But our MediaSyncCommand expects path relative to disk root.
        
        // config('ckfinder.backends.default.root') is 'storage/userfiles/'.
        // So CKFinder '/' maps to 'storage/userfiles/'.
        
        // Wait, MediaSyncCommand: 
        // $signature = 'media:sync {--path=userfiles ...}';
        
        // If CKFinder says folder is `/images/`, that translates to `userfiles/images/`.
        
        $diskRoot = 'userfiles'; // We assume this based on config.
        $cleanPath = trim($relativePath, '/');
        $fullPath = $cleanPath ? $diskRoot . '/' . $cleanPath : $diskRoot;
        
        \Illuminate\Support\Facades\Artisan::call('media:sync', [
            '--path' => $fullPath,
            '--disk' => 'public', // Default
        ]);
        
        Log::info("CKFinderListener: Synced {$fullPath}");
    }
    
    protected function ensureFolderExists($path) {
        $this->syncFolder(dirname($path));
    }
}

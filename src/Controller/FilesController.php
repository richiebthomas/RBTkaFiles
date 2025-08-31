<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\Utility\Text;
use App\Model\Table\PrintJobsTable;

/**
 * Files Controller
 *
 * @property \App\Model\Table\FileItemsTable $FileItems
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\DirectoryNotesTable $DirectoryNotes
 */
class FilesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        
        // Load the FileItems table
        $this->FileItems = $this->fetchTable('FileItems');
        
        // Load the Users table
        $this->Users = $this->fetchTable('Users');
        
        // Load the DirectoryNotes table
        $this->DirectoryNotes = $this->fetchTable('DirectoryNotes');
        
        // Load the PrintJobs table
        $this->PrintJobs = $this->fetchTable('PrintJobs');
        
        // Set response headers for AJAX requests
        if ($this->request->is(['ajax', 'json'])) {
            $this->response = $this->response->withHeader('Access-Control-Allow-Origin', '*');
            $this->response = $this->response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $this->response = $this->response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }
    }



    /**
     * Main file manager view
     */
    public function index()
    {
        // Get all path segments after /files/
        $request = $this->getRequest();
        $pathSegments = $request->getParam('pass', []);
        $path = implode('/', $pathSegments);
        
        $path = $path ? urldecode($path) : '';
        $path = $this->sanitizePath($path);

        if ($this->request->is('ajax')) {
            return $this->browse();
        }

        $this->set(compact('path'));
        $this->viewBuilder()->setLayout('default');
    }

    /**
     * Browse directory contents via AJAX
     */
    public function browse(): Response
    {
        // Get all path segments after /browse/
        $request = $this->getRequest();
        $pathSegments = $request->getParam('pass', []);
        $path = implode('/', $pathSegments);
        
        $originalPath = $path;
        $path = $path ? urldecode($path) : '';
        $path = $this->sanitizePath($path);

        $this->log('=== BROWSE DEBUG ===', 'debug');
        $this->log('Path segments: ' . json_encode($pathSegments), 'debug');
        $this->log('Joined path: ' . $originalPath, 'debug');
        $this->log('Original path: ' . ($originalPath ?? 'null'), 'debug');
        $this->log('Decoded path: ' . $path, 'debug');
        $this->log('Sanitized path: ' . $path, 'debug');

        try {
            $items = $this->FileItems->findByPath($path)->toArray();
            $breadcrumbs = $this->buildBreadcrumbs($path);
            
            // Get directory notes if they exist
            $notes = [];
            $directoryNote = $this->DirectoryNotes->getByPath($path);
            if ($directoryNote && $directoryNote->notes_data) {
                $notes = is_string($directoryNote->notes_data) ? 
                    json_decode($directoryNote->notes_data, true) : 
                    $directoryNote->notes_data;
            }

            $this->log('Query: findByPath(' . $path . ')', 'debug');
            $this->log('Items found: ' . count($items), 'debug');
            
            foreach ($items as $item) {
                $this->log('Item: ' . $item->name . ' (path: ' . $item->path . ', parent: ' . $item->parent_path . ')', 'debug');
            }

            $response = [
                'success' => true,
                'path' => $path,
                'items' => $items,
                'breadcrumbs' => $breadcrumbs,
                'notes' => $notes,
                'debug' => [
                    'original_path' => $originalPath,
                    'decoded_path' => $path,
                    'sanitized_path' => $path,
                    'items_count' => count($items),
                    'query' => 'parent_path = "' . $path . '"'
                ]
            ];
        } catch (\Exception $e) {
            $this->log('Error in browse: ' . $e->getMessage(), 'error');
            $response = [
                'success' => false,
                'message' => 'Error loading directory: ' . $e->getMessage(),
                'debug' => [
                    'path' => $path,
                    'error' => $e->getMessage()
                ]
            ];
        }

        return $this->jsonResponse($response);
    }

    /**
     * Simple test method
     */
    public function test()
    {
        $this->viewBuilder()->setLayout(false);
    }

    /**
     * Debug endpoint to list all files in database
     */
    public function debugFiles(): Response
    {
        $files = $this->FileItems->find()->toArray();
        
        $response = [
            'success' => true,
            'total_files' => count($files),
            'files' => array_map(function($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->name,
                    'path' => $file->path,
                    'type' => $file->type,
                    'size' => $file->size,
                    'mime_type' => $file->mime_type
                ];
            }, $files)
        ];

        return $this->response->withType('application/json')
                             ->withStringBody(json_encode($response));
    }

    /**
     * Test API endpoint to check CSRF
     */
    public function apiTest(): Response
    {
        $response = [
            'success' => true,
            'message' => 'API test successful',
            'method' => $this->request->getMethod(),
            'path' => $this->request->getPath(),
            'data' => $this->request->getData()
        ];

        return $this->response->withType('application/json')
                             ->withStringBody(json_encode($response));
    }

    /**
     * Create a new folder
     */
    public function createFolder(): Response
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();
        $parentPath = $this->sanitizePath($data['parent_path'] ?? '');
        $folderName = trim($data['name'] ?? '');

        if (empty($folderName)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Folder name is required']);
        }

        // Sanitize folder name
        $folderName = $this->sanitizeFileName($folderName);
        $folderPath = $parentPath ? $parentPath . '/' . $folderName : $folderName;

        if ($this->FileItems->pathExists($folderPath)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Folder already exists']);
        }

        $folder = $this->FileItems->newEntity([
            'name' => $folderName,
            'type' => 'folder',
            'path' => $folderPath,
            'parent_path' => $parentPath,
        ]);

        if ($this->FileItems->save($folder)) {
            // Create physical directory
            $this->ensurePhysicalDirectory($folderPath);
            
            return $this->jsonResponse(['success' => true, 'message' => 'Folder created successfully', 'item' => $folder]);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Failed to create folder']);
    }

    /**
     * Upload files
     */
    public function upload(): Response
    {
        // Debug logging
        $this->log('Upload request received', 'debug');
        $this->log('Request method: ' . $this->request->getMethod(), 'debug');
        $this->log('Request path: ' . $this->request->getPath(), 'debug');
        
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();
        $parentPath = $this->sanitizePath($data['parent_path'] ?? '');
        $uploadedFiles = $this->request->getUploadedFiles();
        
        $this->log('Uploaded files count: ' . count($uploadedFiles), 'debug');

        if (empty($uploadedFiles['files'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'No files uploaded']);
        }

        $results = [];
        $uploadPath = $this->getUploadPath();

        foreach ($uploadedFiles['files'] as $uploadedFile) {
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                $results[] = ['success' => false, 'name' => $uploadedFile->getClientFilename(), 'message' => 'Upload error'];
                continue;
            }
            
            $originalName = $uploadedFile->getClientFilename();
            
            // Check for dangerous file extensions
            if ($this->isDangerousFile($originalName)) {
                $this->log('Blocked dangerous file upload attempt: ' . $originalName, 'warning');
                $results[] = ['success' => false, 'name' => $originalName, 'message' => 'File type not allowed for security reasons'];
                continue;
            }

            // $originalName already set above for dangerous file check
            $safeName = $this->sanitizeFileName($originalName);
            $filePath = $parentPath ? $parentPath . '/' . $safeName : $safeName;

            // Generate unique filename if exists
            $counter = 1;
            $baseName = pathinfo($safeName, PATHINFO_FILENAME);
            $extension = pathinfo($safeName, PATHINFO_EXTENSION);
            while ($this->FileItems->pathExists($filePath)) {
                $newName = $baseName . '_' . $counter . ($extension ? '.' . $extension : '');
                $filePath = $parentPath ? $parentPath . '/' . $newName : $newName;
                $counter++;
            }

            // Generate unique filename for disk storage
            $diskFilename = Text::uuid() . '.' . $extension;
            $fullDiskPath = $uploadPath . DS . $diskFilename;

            try {
                $uploadedFile->moveTo($fullDiskPath);
                
                $fileItem = $this->FileItems->newEntity([
                    'name' => basename($filePath),
                    'type' => 'file',
                    'path' => $filePath,
                    'parent_path' => $parentPath,
                    'mime_type' => $uploadedFile->getClientMediaType(),
                    'size' => $uploadedFile->getSize(),
                    'filename_on_disk' => $diskFilename,
                ]);

                if ($this->FileItems->save($fileItem)) {
                    $results[] = ['success' => true, 'name' => $originalName, 'item' => $fileItem];
                } else {
                    unlink($fullDiskPath); // Clean up file
                    $results[] = ['success' => false, 'name' => $originalName, 'message' => 'Failed to save file info'];
                }
            } catch (\Exception $e) {
                $results[] = ['success' => false, 'name' => $originalName, 'message' => 'Failed to save file'];
            }
        }

        return $this->jsonResponse(['success' => true, 'results' => $results]);
    }

    /**
     * Rename a file or folder
     */
    public function rename(): Response
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();
        $oldPath = $this->sanitizePath($data['old_path'] ?? '');
        $newName = trim($data['new_name'] ?? '');

        if (empty($oldPath) || empty($newName)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Old path and new name are required']);
        }

        $item = $this->FileItems->getByPath($oldPath);
        if (!$item) {
            return $this->jsonResponse(['success' => false, 'message' => 'Item not found']);
        }

        $newName = $this->sanitizeFileName($newName);
        $newPath = dirname($oldPath) === '.' ? $newName : dirname($oldPath) . '/' . $newName;

        if ($this->FileItems->pathExists($newPath)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Item with this name already exists']);
        }

        $item->name = $newName;
        $item->path = $newPath;

        if ($this->FileItems->save($item)) {
            // Update all children if it's a folder
            if ($item->isFolder()) {
                $this->updateChildrenPaths($oldPath, $newPath);
                // Update directory notes path
                $this->updateDirectoryNotesPath($oldPath, $newPath);
            }
            
            return $this->jsonResponse(['success' => true, 'message' => 'Item renamed successfully', 'item' => $item]);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Failed to rename item']);
    }

    /**
     * Update directory notes path when directory is renamed
     */
    private function updateDirectoryNotesPath(string $oldPath, string $newPath): void
    {
        try {
            // Update the directory's own notes
            $note = $this->DirectoryNotes->getByPath($oldPath);
            if ($note) {
                $note->path = $newPath;
                $this->DirectoryNotes->save($note);
            }

            // Update notes for all subdirectories
            $childNotes = $this->DirectoryNotes->find()
                ->where(['path LIKE' => $oldPath . '/%']);

            foreach ($childNotes as $childNote) {
                $childNote->path = str_replace($oldPath . '/', $newPath . '/', $childNote->path);
                $this->DirectoryNotes->save($childNote);
            }
        } catch (\Exception $e) {
            $this->log('Error updating directory notes paths: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Move a file or folder
     */
    public function move(): Response
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();
        $sourcePath = $this->sanitizePath($data['source_path'] ?? '');
        $targetPath = $this->sanitizePath($data['target_path'] ?? '');

        if (empty($sourcePath)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Source path is required']);
        }

        $sourceItem = $this->FileItems->getByPath($sourcePath);
        if (!$sourceItem) {
            return $this->jsonResponse(['success' => false, 'message' => 'Source item not found']);
        }

        // Prevent moving item into itself or its children
        if ($sourceItem->isFolder() && (
            $targetPath === $sourcePath || 
            strpos($targetPath, $sourcePath . '/') === 0
        )) {
            return $this->jsonResponse(['success' => false, 'message' => 'Cannot move folder into itself']);
        }

        // Generate new path
        $newPath = $targetPath ? $targetPath . '/' . $sourceItem->name : $sourceItem->name;

        // Check if target already exists
        if ($this->FileItems->pathExists($newPath)) {
            return $this->jsonResponse(['success' => false, 'message' => 'An item with this name already exists in the target location']);
        }

        // Update the item
        $sourceItem->path = $newPath;
        $sourceItem->parent_path = $targetPath;

        if ($this->FileItems->save($sourceItem)) {
            // If it's a folder, update all children paths recursively
            if ($sourceItem->isFolder()) {
                $this->updateChildrenPathsAfterMove($sourcePath, $newPath);
                // Update directory notes paths after move
                $this->updateDirectoryNotesPath($sourcePath, $newPath);
            }
            
            return $this->jsonResponse([
                'success' => true, 
                'message' => 'Item moved successfully',
                'item' => $sourceItem
            ]);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Failed to move item']);
    }

    /**
     * Delete a file or folder
     */
    public function delete(): Response
    {
        $this->request->allowMethod(['post', 'delete']);

        $data = $this->request->getData();
        $path = $this->sanitizePath($data['path'] ?? '');

        if (empty($path)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Path is required']);
        }

        $item = $this->FileItems->getByPath($path);
        if (!$item) {
            return $this->jsonResponse(['success' => false, 'message' => 'Item not found']);
        }

        if ($item->isFile()) {
            // Delete physical file
            $uploadPath = $this->getUploadPath();
            $fullPath = $uploadPath . DS . $item->filename_on_disk;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        } else {
            // Delete all children recursively
            $this->deleteChildrenRecursively($path);
        }

        if ($this->FileItems->delete($item)) {
            return $this->jsonResponse(['success' => true, 'message' => 'Item deleted successfully']);
        }

        return $this->jsonResponse(['success' => false, 'message' => 'Failed to delete item']);
    }

    /**
     * Preview a file (serve file content for preview)
     */
    public function preview(): Response
    {
        // Get all path segments after /preview/
        $request = $this->getRequest();
        $pathSegments = $request->getParam('pass', []);
        $path = implode('/', $pathSegments);
        
        $path = urldecode($path);
        $path = $this->sanitizePath($path);

        $item = $this->FileItems->getByPath($path);
        if (!$item || !$item->isFile()) {
            throw new NotFoundException('File not found');
        }

        $uploadPath = $this->getUploadPath();
        $fullPath = $uploadPath . DS . $item->filename_on_disk;

        if (!file_exists($fullPath)) {
            throw new NotFoundException('Physical file not found');
        }

        // Set appropriate content type
        $mimeType = $item->mime_type ?: 'application/octet-stream';
        
        return $this->response->withFile($fullPath)
                             ->withType($mimeType)
                             ->withHeader('Content-Disposition', 'inline; filename="' . $item->name . '"');
    }

    /**
     * Get file information for preview
     */
    public function fileInfo(): Response
    {
        try {
            // Get path from query parameter
            $path = $this->request->getQuery('path', '');
            
            if (empty($path)) {
                return $this->response->withStatus(400)
                    ->withStringBody(json_encode([
                        'error' => 'Path parameter is required',
                        'message' => 'Path parameter is required'
                    ]));
            }
            
            $path = $this->sanitizePath($path);

            $this->log('Getting file info for path: ' . $path, 'debug');

            $item = $this->FileItems->getByPath($path);
            if (!$item) {
                $this->log('File not found in database: ' . $path, 'debug');
                return $this->jsonResponse(['success' => false, 'message' => 'File not found in database']);
            }

            if (!$item->isFile()) {
                $this->log('Item is not a file: ' . $path, 'debug');
                return $this->jsonResponse(['success' => false, 'message' => 'Item is not a file']);
            }

            $this->log('File found: ' . $item->name, 'debug');

            $response = [
                'success' => true,
                'file' => [
                    'name' => $item->name,
                    'path' => $item->path,
                    'size' => $item->size ?: 0,
                    'human_size' => $item->getHumanSize(),
                    'mime_type' => $item->mime_type ?: 'application/octet-stream',
                    'extension' => $item->getExtension(),
                    'modified' => $item->modified ? $item->modified->format('M j, Y g:i A') : 'Unknown',
                    'created' => $item->created ? $item->created->format('M j, Y g:i A') : 'Unknown',
                    'is_previewable' => $this->isPreviewable($item)
                ]
            ];

            return $this->response->withType('application/json')
                                 ->withStringBody(json_encode($response));
        } catch (\Exception $e) {
            $this->log('Error in fileInfo: ' . $e->getMessage(), 'error');
            return $this->jsonResponse([
                'success' => false, 
                'message' => 'Server error: ' . $e->getMessage(),
                'debug' => [
                    'original_path' => $path ?? 'unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
        }
    }

    /**
     * Get list of dangerous file extensions
     */
    private function getDangerousExtensions(): array
    {
        return [
            // Executable files
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'vbe', 'js', 'jse', 'jar',
            'msi', 'msp', 'hta', 'cpl', 'msc', 'wsf', 'wsh', 'ps1', 'ps2', 'psc1', 'psc2',
            // Script files
            'php', 'asp', 'aspx', 'jsp', 'pl', 'py', 'rb', 'sh', 'cgi',
            // Configuration files  
            'htaccess', 'htpasswd', 'ini', 'cfg', 'conf',
            // Archive files that could contain dangerous content
            'zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz',
            // Other potentially dangerous
            'sql', 'db', 'sqlite', 'mdb'
        ];
    }

    /**
     * Check if file extension is dangerous
     */
    private function isDangerousFile(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $this->getDangerousExtensions());
    }

    /**
     * Get user's print history
     */
    public function getUserPrints(): Response
    {
        $this->request->allowMethod(['post']);

        try {
            $data = $this->request->getData();
            $rollNumber = trim($data['roll_number'] ?? '');

            if (empty($rollNumber)) {
                throw new BadRequestException('Roll number is required');
            }

            // Get user
            $user = $this->Users->find()
                ->where(['roll_number' => $rollNumber])
                ->first();

            if (!$user) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            // Get print history
            $printJobs = $this->PrintJobs->findByUserId($user->id)->toArray();

            $response = [
                'success' => true,
                'user' => [
                    'roll_number' => $user->roll_number,
                    'name' => $user->name
                ],
                'print_history' => array_map(function($printJob) {
                    return [
                        'timestamp' => $printJob->timestamp->format('M j, Y g:i A'),
                    ];
                }, $printJobs),
                'total_prints' => count($printJobs)
            ];

            return $this->jsonResponse($response);

        } catch (\Exception $e) {
            $this->log('Error in getUserPrints: ' . $e->getMessage(), 'error');
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Error getting print history: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lookup user by roll number
     */
    public function lookupUser(): Response
    {
        $this->request->allowMethod(['post']);

        try {
            $data = $this->request->getData();
            $rollNumber = trim($data['roll_number'] ?? '');

            if (empty($rollNumber)) {
                throw new BadRequestException('Roll number is required');
            }

            if (strlen($rollNumber) !== 7) {
                return $this->jsonResponse([
                    'success' => false,
                    'user_found' => false,
                    'message' => 'Roll number must be exactly 7 digits'
                ]);
            }

            // Look up user in database
            $user = $this->Users->find()
                ->where(['roll_number' => $rollNumber])
                ->first();

            if ($user) {
                return $this->jsonResponse([
                    'success' => true,
                    'user_found' => true,
                    'user' => [
                        'roll_number' => $user->roll_number,
                        'name' => $user->name
                    ]
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => true,
                    'user_found' => false,
                    'message' => 'User not found - you can enter your name manually'
                ]);
            }

        } catch (\Exception $e) {
            $this->log('Error in lookupUser: ' . $e->getMessage(), 'error');
            return $this->jsonResponse([
                'success' => false,
                'user_found' => false,
                'message' => 'Error looking up user: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Print a PDF with user details
     */
    public function print(): Response
    {
        $this->request->allowMethod(['post']);

        try {
            // Clear temp directory first
            $tempDir = WWW_ROOT . 'temp';
            if (is_dir($tempDir)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                
                foreach ($files as $fileInfo) {
                    $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
                    $todo($fileInfo->getRealPath());
                }
            } else {
                mkdir($tempDir, 0755, true);
            }

            // Get form data
            $data = $this->request->getData();
            $filePath = $this->sanitizePath($data['file'] ?? '');
            $name = ucwords(trim($data['name'] ?? ''));
            $roll = trim($data['roll'] ?? '');
            $lab = strtoupper(trim($data['lab'] ?? ''));

            if (empty($filePath) || empty($name) || empty($roll)) {
                throw new BadRequestException('File path, name, and roll number are required');
            }

            // Get file from database
            $item = $this->FileItems->getByPath($filePath);
            if (!$item || !$item->isFile()) {
                throw new NotFoundException('File not found');
            }

            // Check if file is PDF
            if (strtolower($item->getExtension()) !== 'pdf') {
                throw new BadRequestException('Only PDF files can be printed');
            }

            // Get physical file path
            $uploadPath = $this->getUploadPath();
            $fullPath = $uploadPath . DS . $item->filename_on_disk;

            if (!file_exists($fullPath)) {
                throw new NotFoundException('Physical file not found');
            }

            // Save/update user in database
            $this->saveOrUpdateUser($roll, $name);

            // Update print job tracking
            $this->updatePrintJobs($roll);

            // Process PDF with user details
            $modifiedPdfPath = $this->processPdfForPrint($fullPath, $name, $roll, $lab);

            // Return the modified PDF
            return $this->response->withFile($modifiedPdfPath)
                                 ->withType('application/pdf')
                                 ->withHeader('Content-Disposition', 'inline; filename="printed_' . $item->name . '"');

        } catch (\Exception $e) {
            $this->log('Error in print: ' . $e->getMessage(), 'error');
            return $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Download a file
     */
    public function download(): Response
    {
        // Get all path segments after /download/
        $request = $this->getRequest();
        $pathSegments = $request->getParam('pass', []);
        $path = implode('/', $pathSegments);
        
        $path = urldecode($path);
        $path = $this->sanitizePath($path);

        $item = $this->FileItems->getByPath($path);
        if (!$item || !$item->isFile()) {
            throw new NotFoundException('File not found');
        }

        $uploadPath = $this->getUploadPath();
        $fullPath = $uploadPath . DS . $item->filename_on_disk;

        if (!file_exists($fullPath)) {
            throw new NotFoundException('Physical file not found');
        }

        return $this->response->withFile($fullPath)
                             ->withDownload($item->name);
    }

    /**
     * Save or update directory notes
     */
    public function saveNotes(): Response
    {
        $this->request->allowMethod(['post']);

        try {
            $data = $this->request->getData();
            $path = $this->sanitizePath($data['path'] ?? '');
            $content = trim($data['content'] ?? '');
            $noteId = $data['noteId'] ?? null;
            $action = $data['action'] ?? 'edit';

            $this->log('=== SAVE NOTES DEBUG ===', 'debug');
            $this->log('Raw request data: ' . json_encode($this->request->getData()), 'debug');
            $this->log('Request data: ' . json_encode($data), 'debug');
            $this->log('Path: ' . $path, 'debug');
            $this->log('Content: ' . $content, 'debug');
            $this->log('Note ID: ' . $noteId, 'debug');
            $this->log('Action: ' . $action, 'debug');
            $this->log('Request method: ' . $this->request->getMethod(), 'debug');
            $this->log('Content type: ' . $this->request->getHeaderLine('Content-Type'), 'debug');

            // Allow root path ('') for homepage notes
            // Previously: empty path was rejected

            if ($action === 'add' && empty($content)) {
                throw new \Exception('Content is required for add action');
            }

            // Get existing notes or create new array
            $directoryNote = $this->DirectoryNotes->getByPath($path);
            $this->log('DirectoryNote from database: ' . json_encode($directoryNote), 'debug');
            
            if (!$directoryNote) {
                $this->log('No existing directory note found, creating new one', 'debug');
                $directoryNote = $this->DirectoryNotes->newEntity([
                    'path' => $path,
                    'notes_data' => []
                ]);
            } else {
                $this->log('Existing directory note found', 'debug');
                $this->log('DirectoryNote notes_data raw: ' . json_encode($directoryNote->notes_data), 'debug');
                $this->log('DirectoryNote notes_data type: ' . gettype($directoryNote->notes_data), 'debug');
                
                // Check if notes_data is a string (JSON) that needs decoding
                if (is_string($directoryNote->notes_data)) {
                    $this->log('Notes_data is a string, attempting to decode JSON', 'debug');
                    $decoded = json_decode($directoryNote->notes_data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $this->log('JSON decode successful: ' . json_encode($decoded), 'debug');
                        $directoryNote->notes_data = $decoded;
                    } else {
                        $this->log('JSON decode failed: ' . json_last_error_msg(), 'debug');
                        $directoryNote->notes_data = [];
                    }
                }
            }

            $notesData = $directoryNote->notes_data ?? [];
            if (!is_array($notesData)) {
                $this->log('Notes data is not an array, converting from: ' . gettype($notesData), 'debug');
                $notesData = [];
            }

            $this->log('Current notes data: ' . json_encode($notesData), 'debug');
            $this->log('Notes data type: ' . gettype($notesData), 'debug');
            $this->log('Notes data count: ' . count($notesData), 'debug');

            switch ($action) {
                case 'add':
                    $this->log('Add case - notesData before: ' . json_encode($notesData), 'debug');
                    $this->log('Add case - notesData count before: ' . count($notesData), 'debug');
                    
                    $newNote = [
                        'id' => uniqid(),
                        'content' => $content,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s')
                    ];
                    
                    $this->log('Add case - new note: ' . json_encode($newNote), 'debug');
                    array_unshift($notesData, $newNote);
                    
                    $this->log('Add case - notesData after: ' . json_encode($notesData), 'debug');
                    $this->log('Add case - notesData count after: ' . count($notesData), 'debug');
                    break;



                case 'edit':
                    $this->log('Edit case - noteId: ' . $noteId, 'debug');
                    $this->log('Edit case - notesData before: ' . json_encode($notesData), 'debug');
                    
                    if ($noteId === 'new') {
                        $newNote = [
                            'id' => uniqid(),
                            'content' => $content,
                            'created' => date('Y-m-d H:i:s'),
                            'modified' => date('Y-m-d H:i:s')
                        ];
                        array_unshift($notesData, $newNote);
                        $this->log('Edit case - added new note: ' . json_encode($newNote), 'debug');
                    } else {
                        $noteFound = false;
                        foreach ($notesData as &$note) {
                            $this->log('Edit case - checking note: ' . json_encode($note), 'debug');
                            if ($note['id'] === $noteId) {
                                $this->log('Edit case - found note to edit, updating content', 'debug');
                                $note['content'] = $content;
                                $note['modified'] = date('Y-m-d H:i:s');
                                $noteFound = true;
                                break;
                            }
                        }
                        
                        if (!$noteFound) {
                            $this->log('Edit case - note not found, creating new note', 'debug');
                            $newNote = [
                                'id' => $noteId,
                                'content' => $content,
                                'created' => date('Y-m-d H:i:s'),
                                'modified' => date('Y-m-d H:i:s')
                            ];
                            array_unshift($notesData, $newNote);
                        }
                    }
                    
                    $this->log('Edit case - notesData after: ' . json_encode($notesData), 'debug');
                    break;

                case 'delete':
                    $notesData = array_filter($notesData, function($note) use ($noteId) {
                        return $note['id'] !== $noteId;
                    });
                    $notesData = array_values($notesData); // Reset array keys
                    break;
            }

            $this->log('After switch - notes data: ' . json_encode($notesData), 'debug');
            $this->log('After switch - notes data type: ' . gettype($notesData), 'debug');
            $this->log('After switch - notes data count: ' . count($notesData), 'debug');
            
            $directoryNote->notes_data = $notesData;
            $this->log('Updated notes data: ' . json_encode($notesData), 'debug');

            if ($this->DirectoryNotes->save($directoryNote)) {
                $this->log('Notes saved successfully', 'debug');
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Notes saved successfully',
                    'notes' => $notesData
                ]);
            }

            $errors = $directoryNote->getErrors();
            $this->log('Save failed. Validation errors: ' . json_encode($errors), 'error');
            throw new \Exception('Failed to save notes: ' . json_encode($errors));

        } catch (\Exception $e) {
            $this->log('Error in saveNotes: ' . $e->getMessage(), 'error');
            $this->log($e->getTraceAsString(), 'error');
            return $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Helper methods
     */
    
    private function jsonResponse(array $data): Response
    {
        return $this->response->withType('application/json')
                             ->withStringBody(json_encode($data));
    }

    private function sanitizePath(string $path): string
    {
        // Remove dangerous characters and normalize path
        $path = str_replace(['..', '\\', '//'], ['', '/', '/'], $path);
        $path = trim($path, '/');
        return $path;
    }

    private function sanitizeFileName(string $filename): string
    {
        // Remove dangerous characters from filename
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        $filename = preg_replace('/_{2,}/', '_', $filename);
        return trim($filename, '_');
    }

    private function buildBreadcrumbs(string $path): array
    {
        $breadcrumbs = [['name' => 'Home', 'path' => '']];
        
        if (!empty($path)) {
            $parts = explode('/', $path);
            $currentPath = '';
            
            foreach ($parts as $part) {
                $currentPath = $currentPath ? $currentPath . '/' . $part : $part;
                $breadcrumbs[] = ['name' => $part, 'path' => $currentPath];
            }
        }
        
        return $breadcrumbs;
    }

    private function getUploadPath(): string
    {
        $uploadPath = WWW_ROOT . 'uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        return $uploadPath;
    }

    private function ensurePhysicalDirectory(string $path): void
    {
        $uploadPath = $this->getUploadPath();
        $fullPath = $uploadPath . DS . str_replace('/', DS, $path);
        
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
    }

    private function updateChildrenPaths(string $oldPath, string $newPath): void
    {
        $this->log('=== RENAME CHILDREN DEBUG ===', 'debug');
        $this->log('Updating children paths: ' . $oldPath . ' -> ' . $newPath, 'debug');
        
        $children = $this->FileItems->find()
            ->where(['path LIKE' => $oldPath . '/%'])
            ->toArray();

        $this->log('Found ' . count($children) . ' children to update', 'debug');

        foreach ($children as $child) {
            $this->log('Before: ' . $child->name . ' (path: ' . $child->path . ', parent: ' . $child->parent_path . ')', 'debug');
            
            // Update the child's path
            $newChildPath = str_replace($oldPath . '/', $newPath . '/', $child->path);
            $child->path = $newChildPath;
            
            // Update the child's parent_path
            if ($child->parent_path === $oldPath) {
                // Direct child - parent_path equals the old folder path exactly
                $child->parent_path = $newPath;
            } else if (strpos($child->parent_path, $oldPath . '/') === 0) {
                // Nested child - parent_path starts with old folder path + '/'
                $child->parent_path = str_replace($oldPath . '/', $newPath . '/', $child->parent_path);
            }
            
            $this->log('After: ' . $child->name . ' (path: ' . $child->path . ', parent: ' . $child->parent_path . ')', 'debug');
            
            if (!$this->FileItems->save($child)) {
                $this->log('Failed to save child: ' . $child->name, 'error');
            }
        }
    }

    private function updateChildrenPathsAfterMove(string $oldPath, string $newPath): void
    {
        $children = $this->FileItems->find()
            ->where(['path LIKE' => $oldPath . '/%'])
            ->toArray();

        foreach ($children as $child) {
            // Replace the old parent path with new parent path
            $relativePath = substr($child->path, strlen($oldPath . '/'));
            $child->path = $newPath . '/' . $relativePath;
            
            // Update parent path
            $childParentPath = dirname($child->path);
            $child->parent_path = ($childParentPath === '.') ? '' : $childParentPath;
            
            $this->FileItems->save($child);
        }
    }

    private function deleteChildrenRecursively(string $path): void
    {
        $children = $this->FileItems->find()
            ->where(['path LIKE' => $path . '/%'])
            ->toArray();

        foreach ($children as $child) {
            if ($child->isFile()) {
                $uploadPath = $this->getUploadPath();
                $fullPath = $uploadPath . DS . $child->filename_on_disk;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            $this->FileItems->delete($child);
        }
    }

    /**
     * Check if file type is previewable
     */
    private function isPreviewable($item): bool
    {
        try {
            if (!$item || !$item->mime_type) {
                return false;
            }

            $previewableTypes = [
                // Images
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
                // Text files
                'text/plain', 'text/html', 'text/css', 'text/javascript', 'text/xml',
                'application/json', 'application/xml',
                // Documents
                'application/pdf',
                // Code files
                'text/x-php', 'text/x-python', 'text/x-java', 'text/x-c', 'text/x-cpp',
                'application/x-javascript', 'application/javascript'
            ];

            // Check by mime type
            if (in_array($item->mime_type, $previewableTypes)) {
                return true;
            }

            // Check by extension for common text files
            $extension = strtolower($item->getExtension());
            $textExtensions = [
                'txt', 'md', 'json', 'xml', 'html', 'htm', 'css', 'js', 'php', 'py', 'java', 'c', 'cpp', 'h', 'hpp',
                'sql', 'yml', 'yaml', 'ini', 'conf', 'log', 'csv', 'sh', 'bat', 'ps1'
            ];

            return in_array($extension, $textExtensions);
        } catch (\Exception $e) {
            $this->log('Error in isPreviewable: ' . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Update print job tracking in database
     */
    private function updatePrintJobs(string $roll): void
    {
        try {
            // Get or create user
            $user = $this->Users->find()
                ->where(['roll_number' => $roll])
                ->first();

            if (!$user) {
                // This shouldn't happen since saveOrUpdateUser is called first
                $this->log('Warning: User not found in updatePrintJobs for roll ' . $roll, 'warning');
                return;
            }

            // Create new print job record
            $printJob = $this->PrintJobs->newEntity([
                'user_id' => $user->id,
                'timestamp' => new \Cake\I18n\DateTime(),
            ]);

            if (!$this->PrintJobs->save($printJob)) {
                $this->log('Failed to save print job: ' . json_encode($printJob->getErrors()), 'error');
            }

        } catch (\Exception $e) {
            $this->log('Error updating print jobs: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Save or update user in database
     */
    private function saveOrUpdateUser(string $rollNumber, string $name): void
    {
        try {
            // Check if user already exists
            $user = $this->Users->find()
                ->where(['roll_number' => $rollNumber])
                ->first();

            if ($user) {
                // Update existing user's name if different
                if ($user->name !== $name) {
                    $user->name = $name;
                    $this->Users->save($user);
                    $this->log("Updated user name for roll number {$rollNumber}: {$name}", 'info');
                }
            } else {
                // Create new user
                $user = $this->Users->newEntity([
                    'roll_number' => $rollNumber,
                    'name' => $name,
                ]);
                $this->Users->save($user);
                $this->log("Created new user: {$rollNumber} - {$name}", 'info');
            }
        } catch (\Exception $e) {
            $this->log('Error saving user: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Process PDF with FPDI to add user details
     */
    private function processPdfForPrint(string $originalPath, string $name, string $roll, string $lab): string
    {
        // Create temp directory if it doesn't exist
        $tempDir = WWW_ROOT . 'temp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Clean old temp files (files older than 1 hour)
        $files = glob($tempDir . DS . 'print_*.pdf');
        foreach ($files as $file) {
            if (filemtime($file) < time() - 3600) {
                unlink($file);
            }
        }

        // Check if FPDI is available through composer autoloader
        try {
            if (!class_exists('\setasign\Fpdi\Fpdi')) {
                throw new \Exception('FPDI not available');
            }

            $pdf = new \setasign\Fpdi\Fpdi();
            $pageCount = $pdf->setSourceFile($originalPath);

            // Process first page
            $tplIdx = $pdf->importPage(1);
            $size = $pdf->getTemplateSize($tplIdx);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplIdx);

            // Add user details to top-right corner
            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetTextColor(0, 0, 0);

            $marginRight = 10;
            $marginTop = 10;
            $cellWidth = 60;
            $x = $size['width'] - $cellWidth - $marginRight;
            $y = $marginTop;

            // Build print text
            $printLines = [$name, $roll];
            if (!empty($lab)) {
                $printLines[] = $lab . ' Lab';
            }

            // Add each line
            foreach ($printLines as $line) {
                $pdf->SetXY($x, $y);
                $pdf->Cell($cellWidth, 5, $line, 0, 1, 'R');
                $y += 6;
            }

            // Process remaining pages
            for ($pageNo = 2; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplIdx);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplIdx);
            }

            // Save modified PDF
            $tempFile = $tempDir . DS . 'print_' . uniqid() . '.pdf';
            $pdf->Output($tempFile, 'F');

            return $tempFile;

        } catch (\Exception $e) {
            $this->log('Error processing PDF with FPDI: ' . $e->getMessage(), 'error');
            
            // If FPDI is not available, log the issue and return original
            if (strpos($e->getMessage(), 'FPDI not available') !== false) {
                $this->log('FPDI not installed. Install with: composer require setasign/fpdi setasign/fpdf', 'warning');
            }
            
            // Return original file if processing fails
            return $originalPath;
        }
    }
}

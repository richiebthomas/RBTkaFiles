## **Implementation Overview**

We'll need to:
1. **Install Supabase PHP SDK**
2. **Configure Supabase credentials**
3. **Modify the FileItems model**
4. **Update file operations (upload, download, preview, print)**
5. **Handle file metadata and database updates**



## **Step 1: Install Supabase PHP SDK**


## **Step 1: Add Supabase Dependencies**

Add this to your `composer.json` in the `require` section:
```json
"supabase/supabase-php": "^0.1.0"
```

Then run:
```bash
composer update
```

## **Step 2: Create Supabase Configuration**

create a configuration file for Supabase:

creating a Supabase service class 

## **Step 3: Create Supabase Service Class**

Create a new file `src/Service/SupabaseStorageService.php`:

```php
<?php
declare(strict_types=1);

namespace App\Service;

use Supabase\CreateClient;
use Supabase\GoTrue\GoTrueClient;
use Supabase\Storage\StorageClient;
use Cake\Core\Configure;
use Cake\Log\Log;

class SupabaseStorageService
{
    private $client;
    private $storage;
    private $bucketName;
    
    public function __construct()
    {
        $this->bucketName = Configure::read('Supabase.bucket_name') ?? 'files';
        
        try {
            $this->client = CreateClient::create(
                Configure::read('Supabase.url'),
                Configure::read('Supabase.key')
            );
            
            $this->storage = $this->client->getStorage();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Supabase client: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Upload a file to Supabase Storage
     */
    public function uploadFile(string $filePath, string $destinationPath, array $options = []): array
    {
        try {
            $fileContent = file_get_contents($filePath);
            if ($fileContent === false) {
                throw new \Exception('Failed to read file: ' . $filePath);
            }
            
            $response = $this->storage->from($this->bucketName)->upload(
                $destinationPath,
                $fileContent,
                $options
            );
            
            if (isset($response['error'])) {
                throw new \Exception('Upload failed: ' . $response['error']);
            }
            
            return [
                'success' => true,
                'path' => $destinationPath,
                'url' => $this->getPublicUrl($destinationPath),
                'size' => filesize($filePath)
            ];
            
        } catch (\Exception $e) {
            Log::error('Supabase upload failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Download a file from Supabase Storage
     */
    public function downloadFile(string $filePath): ?string
    {
        try {
            $response = $this->storage->from($this->bucketName)->download($filePath);
            
            if (isset($response['error'])) {
                throw new \Exception('Download failed: ' . $response['error']);
            }
            
            return $response['data'] ?? null;
            
        } catch (\Exception $e) {
            Log::error('Supabase download failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Delete a file from Supabase Storage
     */
    public function deleteFile(string $filePath): bool
    {
        try {
            $response = $this->storage->from($this->bucketName)->remove([$filePath]);
            
            if (isset($response['error'])) {
                throw new \Exception('Delete failed: ' . $response['error']);
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Supabase delete failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get public URL for a file
     */
    public function getPublicUrl(string $filePath): string
    {
        $baseUrl = Configure::read('Supabase.url');
        $bucketName = $this->bucketName;
        
        return "{$baseUrl}/storage/v1/object/public/{$bucketName}/{$filePath}";
    }
    
    /**
     * Check if file exists in Supabase Storage
     */
    public function fileExists(string $filePath): bool
    {
        try {
            $response = $this->storage->from($this->bucketName)->list('', [
                'search' => $filePath
            ]);
            
            if (isset($response['error'])) {
                return false;
            }
            
            foreach ($response['data'] as $file) {
                if ($file['name'] === basename($filePath)) {
                    return true;
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Supabase file check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get file metadata
     */
    public function getFileMetadata(string $filePath): ?array
    {
        try {
            $response = $this->storage->from($this->bucketName)->list('', [
                'search' => $filePath
            ]);
            
            if (isset($response['error'])) {
                return null;
            }
            
            foreach ($response['data'] as $file) {
                if ($file['name'] === basename($filePath)) {
                    return [
                        'size' => $file['metadata']['size'] ?? 0,
                        'mime_type' => $file['metadata']['mimetype'] ?? 'application/octet-stream',
                        'created_at' => $file['created_at'] ?? null,
                        'updated_at' => $file['updated_at'] ?? null
                    ];
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Supabase metadata check failed: ' . $e->getMessage());
            return null;
        }
    }
}
```

## **Step 4: Add Supabase Configuration**

Add this to your `config/app.php` file (you can add it at the end before the closing `];`):

```php
    /*
     * Supabase Configuration
     */
    'Supabase' => [
        'url' => env('SUPABASE_URL'),
        'key' => env('SUPABASE_ANON_KEY'),
        'bucket_name' => env('SUPABASE_BUCKET_NAME', 'files'),
    ],
```

## **Step 5: Create Environment Variables**

Create or update your `.env` file in your project root:

```env
# Supabase Configuration
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key-here
SUPABASE_BUCKET_NAME=files
```

## **Step 6: Update FileItems Model**

modify your FileItems model to work with Supabase storage.

check the FileItems table to see the current structure:


## **Step 7: Update FileItems Table for Supabase**

We need to add a new field to store the Supabase storage path. Let me create a migration for this:

```php
<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddSupabaseStorageFields extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('file_items');
        
        $table->addColumn('supabase_path', 'string', [
            'default' => null,
            'null' => true,
            'limit' => 1000,
            'comment' => 'Path in Supabase storage'
        ]);
        
        $table->addColumn('storage_type', 'string', [
            'default' => 'local',
            'null' => false,
            'limit' => 20,
            'comment' => 'Storage type: local, supabase'
        ]);
        
        $table->update();
    }
}
```

## **Step 8: Update FileItems Entity**

Add these fields to your `FileItem` entity:

```php
// Add to the $_accessible array:
'supabase_path' => true,
'storage_type' => true,

// Add these methods to the FileItem class:
/**
 * Get the actual file path (local or Supabase)
 */
public function getStoragePath(): string
{
    if ($this->storage_type === 'supabase' && $this->supabase_path) {
        return $this->supabase_path;
    }
    
    return $this->filename_on_disk ?? '';
}

/**
 * Check if file is stored in Supabase
 */
public function isSupabaseStored(): bool
{
    return $this->storage_type === 'supabase';
}
```

## **Step 9: Update FilesController for Supabase**

modify your FilesController to use Supabase storage. Here are the key methods that need updating:

### **Upload Method Update:**

```php
public function upload(): Response
{
    $this->request->allowMethod(['post']);
    
    try {
        $files = $this->request->getUploadedFiles();
        $parentPath = $this->request->getData('parent_path', '');
        
        if (empty($files)) {
            throw new BadRequestException('No files uploaded');
        }
        
        $uploadedFiles = [];
        $supabaseService = new \App\Service\SupabaseStorageService();
        
        foreach ($files as $file) {
            if ($file->getError() === UPLOAD_ERR_OK) {
                $filename = $file->getClientFilename();
                $filePath = $parentPath ? $parentPath . '/' . $filename : $filename;
                
                // Generate unique filename for Supabase
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $basename = pathinfo($filename, PATHINFO_FILENAME);
                $uniqueFilename = $basename . '_' . uniqid() . '.' . $extension;
                $supabasePath = $parentPath ? $parentPath . '/' . $uniqueFilename : $uniqueFilename;
                
                // Upload to Supabase
                $uploadResult = $supabaseService->uploadFile(
                    $file->getStream()->getMetadata('uri'),
                    $supabasePath,
                    [
                        'contentType' => $file->getClientMediaType(),
                        'upsert' => false
                    ]
                );
                
                if (!$uploadResult['success']) {
                    throw new \Exception('Failed to upload to Supabase: ' . $uploadResult['error']);
                }
                
                // Save to database
                $fileItem = $this->FileItems->newEntity([
                    'name' => $filename,
                    'type' => 'file',
                    'path' => $filePath,
                    'parent_path' => $parentPath ?: null,
                    'mime_type' => $file->getClientMediaType(),
                    'size' => $file->getSize(),
                    'filename_on_disk' => $uniqueFilename,
                    'supabase_path' => $supabasePath,
                    'storage_type' => 'supabase'
                ]);
                
                if (!$this->FileItems->save($fileItem)) {
                    // Delete from Supabase if database save fails
                    $supabaseService->deleteFile($supabasePath);
                    throw new \Exception('Failed to save file record to database');
                }
                
                $uploadedFiles[] = $filename;
            }
        }
        
        return $this->jsonResponse([
            'success' => true,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'files' => $uploadedFiles
        ]);
        
    } catch (\Exception $e) {
        $this->log('Error in upload: ' . $e->getMessage(), 'error');
        return $this->jsonResponse([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
```

### **Download Method Update:**

```php
public function download(): Response
{
    $request = $this->getRequest();
    $pathSegments = $request->getParam('pass', []);
    $path = implode('/', $pathSegments);
    
    $path = urldecode($path);
    $path = $this->sanitizePath($path);

    $item = $this->FileItems->getByPath($path);
    if (!$item || !$item->isFile()) {
        throw new NotFoundException('File not found');
    }

    if ($item->isSupabaseStored()) {
        // Download from Supabase
        $supabaseService = new \App\Service\SupabaseStorageService();
        $fileContent = $supabaseService->downloadFile($item->supabase_path);
        
        if ($fileContent === null) {
            throw new NotFoundException('File not found in storage');
        }
        
        return $this->response
            ->withStringBody($fileContent)
            ->withType($item->mime_type)
            ->withHeader('Content-Disposition', 'attachment; filename="' . $item->name . '"');
    } else {
        // Fallback to local storage
        $uploadPath = $this->getUploadPath();
        $fullPath = $uploadPath . DS . $item->filename_on_disk;

        if (!file_exists($fullPath)) {
            throw new NotFoundException('Physical file not found');
        }

        return $this->response->withFile($fullPath);
    }
}
```

### **Delete Method Update:**

```php
public function delete(): Response
{
    $this->request->allowMethod(['post', 'delete']);
    
    try {
        $data = $this->request->getData();
        $path = $this->sanitizePath($data['path'] ?? '');
        
        if (empty($path)) {
            throw new BadRequestException('Path is required');
        }
        
        $item = $this->FileItems->getByPath($path);
        if (!$item) {
            throw new NotFoundException('Item not found');
        }
        
        if ($item->isFolder()) {
            // Handle folder deletion recursively
            $this->deleteFolderRecursive($path);
        } else {
            // Delete file
            if ($item->isSupabaseStored()) {
                $supabaseService = new \App\Service\SupabaseStorageService();
                if (!$supabaseService->deleteFile($item->supabase_path)) {
                    $this->log('Warning: Failed to delete file from Supabase: ' . $item->supabase_path, 'warning');
                }
            } else {
                // Delete local file
                $uploadPath = $this->getUploadPath();
                $fullPath = $uploadPath . DS . $item->filename_on_disk;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            $this->FileItems->delete($item);
        }
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
        
    } catch (\Exception $e) {
        $this->log('Error in delete: ' . $e->getMessage(), 'error');
        return $this->jsonResponse([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
```

## **Step 10: Migration Strategy**

To migrate existing files to Supabase:

1. **Create a migration script** that uploads existing local files to Supabase
2. **Update database records** to point to Supabase paths
3. **Verify all files** are accessible from Supabase
4. **Remove local files** once migration is complete

## **Benefits of This Implementation**

1. **Scalability**: No more local storage limitations
2. **Reliability**: Supabase handles backups and redundancy
3. **Performance**: CDN distribution for faster file access
4. **Cost-effective**: Pay only for storage used
5. **Security**: Supabase handles authentication and access control

## **Next Steps**

1. Install Supabase PHP client: `composer require supabase/supabase-php`
2. Set up your Supabase project and get credentials
3. Create the storage bucket in Supabase
4. Run the database migration
5. Test with a few files before migrating everything
6. Update your environment variables
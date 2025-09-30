<?php
declare(strict_types=1);

namespace App\Service;

use Supabase\CreateClient;
use Cake\Core\Configure;
use Cake\Log\Log;

class SupabaseStorageService
{
    private $client;
    private $storage;
    private $bucketName;
    
    public function getBucketName(): string
    {
        return $this->bucketName;
    }
    
    public function __construct()
    {
        $this->bucketName = Configure::read('Supabase.bucket_name') ?? 'files';
        
        try {
            // Extract reference ID from URL (everything before .supabase.co)
            $url = Configure::read('Supabase.url');
            if (empty($url)) {
                throw new \Exception('Supabase URL not configured');
            }
            
            $referenceId = str_replace(['https://', '.supabase.co'], '', $url);
            
            $apiKey = Configure::read('Supabase.key');
            if (empty($apiKey)) {
                throw new \Exception('Supabase API key not configured');
            }
            
            $this->client = new CreateClient(
                $apiKey,
                $referenceId
            );
            
            $this->storage = $this->client->storage;
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
            
            // Check if response indicates success (status code 200-299)
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return [
                    'success' => true,
                    'path' => $destinationPath,
                    'url' => $this->getPublicUrl($destinationPath),
                    'size' => filesize($filePath)
                ];
            } else {
                $errorBody = $response->getBody()->getContents();
                throw new \Exception('Upload failed: ' . $errorBody);
            }
            
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
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return $response->getBody()->getContents();
            } else {
                throw new \Exception('Download failed: ' . $response->getBody()->getContents());
            }
            
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
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return true;
            } else {
                throw new \Exception('Delete failed: ' . $response->getBody()->getContents());
            }
            
        } catch (\Exception $e) {
            Log::error('Supabase delete failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rename a file in Supabase Storage (by copying and deleting)
     */
    public function renameFile(string $oldPath, string $newPath): bool
    {
        try {
            // Use the move method instead of download/upload/delete
            $response = $this->storage->from($this->bucketName)->move($oldPath, $newPath);
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return true;
            } else {
                throw new \Exception('Rename failed: ' . $response->getBody()->getContents());
            }
            
        } catch (\Exception $e) {
            Log::error('Supabase rename failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get public URL for a file
     */
    public function getPublicUrl(string $filePath): string
    {
        return $this->storage->from($this->bucketName)->getPublicUrl($filePath);
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
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $data = json_decode($response->getBody()->getContents(), true);
                if (isset($data['data'])) {
                    foreach ($data['data'] as $file) {
                        if ($file['name'] === basename($filePath)) {
                            return true;
                        }
                    }
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
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $data = json_decode($response->getBody()->getContents(), true);
                if (isset($data['data'])) {
                    foreach ($data['data'] as $file) {
                        if ($file['name'] === basename($filePath)) {
                            return [
                                'size' => $file['metadata']['size'] ?? 0,
                                'mime_type' => $file['metadata']['mimetype'] ?? 'application/octet-stream',
                                'created_at' => $file['created_at'] ?? null,
                                'updated_at' => $file['updated_at'] ?? null
                            ];
                        }
                    }
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Supabase metadata check failed: ' . $e->getMessage());
            return null;
        }
    }
}

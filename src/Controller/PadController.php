<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Utility\Text;

/**
 * Pad Controller
 * 
 * Simple Firepad editor page
 */
class PadController extends Controller
{
    /**
     * Index method - displays the Firepad editor
     */
    public function index()
    {
        $this->viewBuilder()->setLayout('pad');
    }

    /**
     * Upload image for Firepad
     */
    public function uploadImage(): Response
    {
        $this->request->allowMethod(['post']);
        
        try {
            $uploadedFiles = $this->request->getUploadedFiles();
            
            if (empty($uploadedFiles['image'])) {
                return $this->jsonResponse(['success' => false, 'message' => 'No image uploaded']);
            }
            
            $imageFile = $uploadedFiles['image'];
            
            if ($imageFile->getError() !== UPLOAD_ERR_OK) {
                return $this->jsonResponse(['success' => false, 'message' => 'Upload error']);
            }
            
            // Validate image type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mimeType = $imageFile->getClientMediaType();
            
            if (!in_array($mimeType, $allowedTypes)) {
                return $this->jsonResponse(['success' => false, 'message' => 'Invalid image type']);
            }
            
            // Generate unique filename
            $extension = pathinfo($imageFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = Text::uuid() . '.' . $extension;
            $uploadPath = WWW_ROOT . 'uploads' . DS . 'docimages' . DS . $filename;
            
            // Move uploaded file
            $imageFile->moveTo($uploadPath);
            
            // Return the public URL
            $publicUrl = '/uploads/docimages/' . $filename;
            
            return $this->jsonResponse([
                'success' => true,
                'url' => $publicUrl,
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Helper method to return JSON response
     */
    private function jsonResponse(array $data): Response
    {
        return $this->response->withType('application/json')
            ->withStringBody(json_encode($data));
    }
}

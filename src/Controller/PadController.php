<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Utility\Text;

/**
 * Pad Controller
 * 
 * Multi-pad Firepad editor
 */
class PadController extends Controller
{
    /**
     * Initialize method
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Pads = $this->fetchTable('Pads');
    }

    /**
     * Index method - displays the Firepad editor with pad list
     * If padId is provided, opens that pad. Otherwise creates/opens default pad.
     */
    public function index($padId = null)
    {
        
        // Get all pads ordered by most recently modified
        $pads = $this->Pads->find('all')
            ->orderBy(['modified' => 'DESC'])
            ->all();
        
        // If no pad ID specified, get the most recent one or create a default
        if ($padId === null) {
            $currentPad = $pads->first();
            
            if (!$currentPad) {
                // Create a default pad if none exist
                $currentPad = $this->Pads->newEntity([
                    'name' => 'My First Pad'
                ]);
                $this->Pads->save($currentPad);
            }
        } else {
            // Load the specified pad
            try {
                $currentPad = $this->Pads->get($padId);
            } catch (\Exception $e) {
                $this->Flash->error('Pad not found.');
                return $this->redirect(['action' => 'index']);
            }
        }
        
        $this->set(compact('pads', 'currentPad'));
        $this->viewBuilder()->setLayout('pad');
    }

    /**
     * Create a new pad
     */
    public function create(): Response
    {
        $this->request->allowMethod(['post']);
        
        $name = $this->request->getData('name');
        
        if (empty($name)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Pad name is required']);
        }
        
        $pad = $this->Pads->newEntity(['name' => $name]);
        
        if ($this->Pads->save($pad)) {
            return $this->jsonResponse([
                'success' => true,
                'pad' => [
                    'id' => $pad->id,
                    'name' => $pad->name,
                    'created' => $pad->created->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                    'modified' => $pad->modified->i18nFormat('yyyy-MM-dd HH:mm:ss')
                ]
            ]);
        }
        
        return $this->jsonResponse(['success' => false, 'message' => 'Failed to create pad']);
    }
    
    /**
     * Delete a pad
     */
    public function delete($padId = null): Response
    {
        $this->request->allowMethod(['post', 'delete']);
        
        if (!$padId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Pad ID is required']);
        }
        
        try {
            $pad = $this->Pads->get($padId);
            
            if ($this->Pads->delete($pad)) {
                return $this->jsonResponse(['success' => true]);
            }
            
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to delete pad']);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Pad not found']);
        }
    }
    
    /**
     * Rename a pad
     */
    public function rename($padId = null): Response
    {
        $this->request->allowMethod(['post']);
        
        if (!$padId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Pad ID is required']);
        }
        
        $name = $this->request->getData('name');
        
        if (empty($name)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Pad name is required']);
        }
        
        try {
            $pad = $this->Pads->get($padId);
            $pad->name = $name;
            
            if ($this->Pads->save($pad)) {
                return $this->jsonResponse(['success' => true]);
            }
            
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to rename pad']);
        } catch (\Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => 'Pad not found']);
        }
    }
    
    /**
     * Get all pads as JSON
     */
    public function list(): Response
    {
        $this->request->allowMethod(['get']);
        
        $pads = $this->Pads->find('all')
            ->orderBy(['modified' => 'DESC'])
            ->all();
        
        $padData = [];
        foreach ($pads as $pad) {
            $padData[] = [
                'id' => $pad->id,
                'name' => $pad->name,
                'created' => $pad->created->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'modified' => $pad->modified->i18nFormat('yyyy-MM-dd HH:mm:ss')
            ];
        }
        
        return $this->jsonResponse(['success' => true, 'pads' => $padData]);
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

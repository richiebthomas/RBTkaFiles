<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;

/**
 * Search Controller
 *
 * @property \App\Model\Table\FileItemsTable $FileItems
 * @property \App\Model\Table\DirectoryNotesTable $DirectoryNotes
 */
class SearchController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        
        // Load the FileItems table
        $this->FileItems = $this->fetchTable('FileItems');
        
        // Load the DirectoryNotes table
        $this->DirectoryNotes = $this->fetchTable('DirectoryNotes');
        
        // Set response headers for AJAX requests
        if ($this->request->is(['ajax', 'json'])) {
            $this->response = $this->response->withHeader('Access-Control-Allow-Origin', '*');
            $this->response = $this->response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $this->response = $this->response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }
    }

    /**
     * Real-time search suggestions
     */
    public function suggestions(): Response
    {
        $this->request->allowMethod(['get', 'post']);
        
        $query = $this->request->getQuery('q', '');
        $query = trim($query);
        
        if (empty($query)) {
            return $this->jsonResponse([
                'success' => true,
                'suggestions' => [],
                'query' => $query
            ]);
        }

        try {
            $suggestions = $this->performSmartSearch($query);
            
            return $this->jsonResponse([
                'success' => true,
                'suggestions' => $suggestions,
                'query' => $query,
                'count' => count($suggestions)
            ]);
            
        } catch (\Exception $e) {
            $this->log('Error in search suggestions: ' . $e->getMessage(), 'error');
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Search error: ' . $e->getMessage(),
                'suggestions' => []
            ]);
        }
    }

    /**
     * Perform smart search with fuzzy matching and path validation
     */
    private function performSmartSearch(string $query): array
    {
        // Normalize query for better matching
        $normalizedQuery = $this->normalizeQuery($query);
        $queryWords = explode(' ', $normalizedQuery);
        
        // Get all files and folders from database
        $allItems = $this->FileItems->find()
            ->orderAsc('type')  // folders first
            ->orderAsc('name')
            ->toArray();

        // Get all directory notes from database
        $allNotes = $this->DirectoryNotes->find()
            ->toArray();

        $suggestions = [];
        $maxSuggestions = 15; // Increased to accommodate notes
        
        // Search through files and folders
        foreach ($allItems as $item) {
            // Validate that all ancestors exist in database
            if (!$this->validateAncestorPath($item->path)) {
                continue;
            }
            
            $score = $this->calculateRelevanceScore($item, $query, $normalizedQuery, $queryWords);
            
            if ($score > 0) {
                $suggestions[] = [
                    'item' => $item,
                    'score' => $score,
                    'match_type' => $this->getMatchType($item, $query, $normalizedQuery),
                    'highlighted_name' => $this->highlightMatches($item->name, $queryWords),
                    'path_parts' => $this->getPathParts($item->path),
                    'type' => 'file_or_folder'
                ];
            }
        }
        
        // Search through directory notes
        foreach ($allNotes as $note) {
            // Validate that the directory containing the notes exists as a folder in FileItems
            if (!$this->validateNotesDirectoryExists($note->path)) {
                continue;
            }
            
            $noteResults = $this->searchInNotes($note, $query, $normalizedQuery, $queryWords);
            $suggestions = array_merge($suggestions, $noteResults);
        }
        
        // Sort by relevance score (highest first)
        usort($suggestions, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        // Limit results
        return array_slice($suggestions, 0, $maxSuggestions);
    }

    /**
     * Validate that all ancestor paths exist in database
     */
    private function validateAncestorPath(string $path): bool
    {
        if (empty($path)) {
            return true; // Root path is always valid
        }
        
        $pathParts = explode('/', $path);
        $currentPath = '';
        
        // Check each ancestor path
        for ($i = 0; $i < count($pathParts) - 1; $i++) {
            $currentPath = $currentPath ? $currentPath . '/' . $pathParts[$i] : $pathParts[$i];
            
            if (!$this->FileItems->pathExists($currentPath)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Validate that the directory containing notes exists as a folder in FileItems
     */
    private function validateNotesDirectoryExists(string $notesPath): bool
    {
        if (empty($notesPath)) {
            // Root path - check if there's a folder at root level
            return $this->FileItems->exists(['type' => 'folder', 'parent_path' => '']);
        }
        
        // Check if the directory path exists as a folder in FileItems
        $directoryItem = $this->FileItems->getByPath($notesPath);
        
        if (!$directoryItem || !$directoryItem->isFolder()) {
            return false;
        }
        
        // Also validate all ancestor paths exist
        return $this->validateAncestorPath($notesPath);
    }

    /**
     * Search through directory notes
     */
    private function searchInNotes($note, string $originalQuery, string $normalizedQuery, array $queryWords): array
    {
        $results = [];
        
        // Handle both array and JSON string formats
        $notesData = $note->notes_data;
        if (is_string($notesData)) {
            $notesData = json_decode($notesData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $results;
            }
        }
        
        if (!$notesData || !is_array($notesData)) {
            return $results;
        }
        
        foreach ($notesData as $noteItem) {
            if (!isset($noteItem['content']) || empty($noteItem['content'])) {
                continue;
            }
            
            $content = $noteItem['content'];
            $score = $this->calculateNoteRelevanceScore($content, $originalQuery, $normalizedQuery, $queryWords);
            
            if ($score > 0) {
                $results[] = [
                    'item' => [
                        'id' => $noteItem['id'] ?? uniqid(),
                        'name' => 'Note: ' . $this->truncateText($content, 50),
                        'type' => 'note',
                        'path' => $note->path,
                        'content' => $content,
                        'created' => $noteItem['created'] ?? null,
                        'modified' => $noteItem['modified'] ?? null
                    ],
                    'score' => $score,
                    'match_type' => $this->getNoteMatchType($content, $originalQuery, $normalizedQuery),
                    'highlighted_name' => $this->highlightMatches($this->truncateText($content, 50), $queryWords),
                    'highlighted_content' => $this->highlightMatches($content, $queryWords),
                    'path_parts' => $this->getPathParts($note->path),
                    'type' => 'note'
                ];
            }
        }
        return $results;
    }

    /**
     * Calculate relevance score for a note
     */
    private function calculateNoteRelevanceScore(string $content, string $originalQuery, string $normalizedQuery, array $queryWords): float
    {
        $score = 0;
        $contentLower = strtolower($content);
        $queryLower = strtolower($originalQuery);
        
        // Exact content match (highest priority)
        if ($contentLower === $queryLower) {
            $score += 90;
        }
        
        // Content starts with query
        if (strpos($contentLower, $queryLower) === 0) {
            $score += 70;
        }
        
        // Content contains query
        if (strpos($contentLower, $queryLower) !== false) {
            $score += 50;
        }
        
        // Fuzzy matching for each word
        foreach ($queryWords as $word) {
            $word = strtolower($word);
            
            // Exact word match in content
            if (strpos($contentLower, $word) !== false) {
                $score += 30;
            }
            
            // Fuzzy word match (allows for typos and variations)
            if ($this->fuzzyMatch($contentLower, $word)) {
                $score += 20;
            }
        }
        
        // Bonus for shorter content (more specific)
        $contentLength = strlen($content);
        if ($contentLength < 100) {
            $score += 10;
        } elseif ($contentLength < 500) {
            $score += 5;
        }
        
        return $score;
    }

    /**
     * Get match type for notes
     */
    private function getNoteMatchType(string $content, string $originalQuery, string $normalizedQuery): string
    {
        $contentLower = strtolower($content);
        $queryLower = strtolower($originalQuery);
        
        if ($contentLower === $queryLower) {
            return 'exact';
        }
        
        if (strpos($contentLower, $queryLower) === 0) {
            return 'starts_with';
        }
        
        if (strpos($contentLower, $queryLower) !== false) {
            return 'contains';
        }
        
        return 'fuzzy';
    }

    /**
     * Truncate text for display
     */
    private function truncateText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }

    /**
     * Calculate relevance score for an item
     */
    private function calculateRelevanceScore($item, string $originalQuery, string $normalizedQuery, array $queryWords): float
    {
        $score = 0;
        $name = strtolower($item->name);
        $path = strtolower($item->path);
        
        // Exact name match (highest priority)
        if ($name === strtolower($originalQuery)) {
            $score += 100;
        }
        
        // Starts with query
        if (strpos($name, strtolower($originalQuery)) === 0) {
            $score += 80;
        }
        
        // Contains query
        if (strpos($name, strtolower($originalQuery)) !== false) {
            $score += 60;
        }
        
        // Fuzzy matching for each word
        foreach ($queryWords as $word) {
            $word = strtolower($word);
            
            // Exact word match in name
            if (strpos($name, $word) !== false) {
                $score += 40;
            }
            
            // Fuzzy word match (allows for typos and variations)
            if ($this->fuzzyMatch($name, $word)) {
                $score += 25;
            }
            
            // Word appears in path
            if (strpos($path, $word) !== false) {
                $score += 15;
            }
        }
        
        // Bonus for folders (they contain other items)
        if ($item->isFolder()) {
            $score += 10;
        }
        
        // Bonus for shorter paths (more specific)
        $pathDepth = substr_count($item->path, '/');
        $score += max(0, 10 - $pathDepth);
        
        return $score;
    }

    /**
     * Simple fuzzy matching algorithm
     */
    private function fuzzyMatch(string $text, string $pattern): bool
    {
        $text = strtolower($text);
        $pattern = strtolower($pattern);
        
        // If pattern is too short, require exact match
        if (strlen($pattern) < 3) {
            return strpos($text, $pattern) !== false;
        }
        
        // Check if all characters in pattern appear in order in text
        $patternIndex = 0;
        $textIndex = 0;
        
        while ($textIndex < strlen($text) && $patternIndex < strlen($pattern)) {
            if ($text[$textIndex] === $pattern[$patternIndex]) {
                $patternIndex++;
            }
            $textIndex++;
        }
        
        return $patternIndex === strlen($pattern);
    }

    /**
     * Determine the type of match
     */
    private function getMatchType($item, string $originalQuery, string $normalizedQuery): string
    {
        $name = strtolower($item->name);
        $query = strtolower($originalQuery);
        
        if ($name === $query) {
            return 'exact';
        }
        
        if (strpos($name, $query) === 0) {
            return 'starts_with';
        }
        
        if (strpos($name, $query) !== false) {
            return 'contains';
        }
        
        return 'fuzzy';
    }

    /**
     * Highlight matching parts of the name
     */
    private function highlightMatches(string $name, array $queryWords): string
    {
        $highlighted = $name;
        
        foreach ($queryWords as $word) {
            $word = preg_quote($word, '/');
            $highlighted = preg_replace(
                "/($word)/i",
                '<mark>$1</mark>',
                $highlighted
            );
        }
        
        return $highlighted;
    }

    /**
     * Get path parts for display
     */
    private function getPathParts(string $path): array
    {
        if (empty($path)) {
            return ['Home'];
        }
        
        $parts = explode('/', $path);
        array_unshift($parts, 'Home');
        
        return $parts;
    }

    /**
     * Normalize query for better matching
     */
    private function normalizeQuery(string $query): string
    {
        // Convert to lowercase
        $query = strtolower($query);
        
        // Remove extra spaces
        $query = preg_replace('/\s+/', ' ', $query);
        
        // Trim
        $query = trim($query);
        
        return $query;
    }

    /**
     * Test endpoint to verify search controller is working
     */
    public function test(): Response
    {
        return $this->jsonResponse([
            'success' => true,
            'message' => 'SearchController is working',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * JSON response helper
     */
    private function jsonResponse(array $data): Response
    {
        return $this->response->withType('application/json')
                             ->withStringBody(json_encode($data));
    }
}

<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;

/**
 * Search Controller
 *
 * @property \App\Model\Table\FileItemsTable $FileItems
 */
class SearchController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        
        // Load the FileItems table
        $this->FileItems = $this->fetchTable('FileItems');
        
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

        $suggestions = [];
        $maxSuggestions = 10;
        
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
                    'path_parts' => $this->getPathParts($item->path)
                ];
            }
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
     * JSON response helper
     */
    private function jsonResponse(array $data): Response
    {
        return $this->response->withType('application/json')
                             ->withStringBody(json_encode($data));
    }
}

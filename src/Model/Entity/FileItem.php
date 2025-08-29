<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FileItem Entity
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $path
 * @property string|null $parent_path
 * @property string|null $mime_type
 * @property int|null $size
 * @property string|null $filename_on_disk
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class FileItem extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'type' => true,
        'path' => true,
        'parent_path' => true,
        'mime_type' => true,
        'size' => true,
        'filename_on_disk' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Check if this item is a folder
     */
    public function isFolder(): bool
    {
        return $this->type === 'folder';
    }

    /**
     * Check if this item is a file
     */
    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    /**
     * Get human readable file size
     */
    public function getHumanSize(): string
    {
        if (!$this->size) {
            return '-';
        }

        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Get file extension
     */
    public function getExtension(): string
    {
        if ($this->isFolder()) {
            return '';
        }
        
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }
}

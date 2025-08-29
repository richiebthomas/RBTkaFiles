<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DirectoryNote Entity
 *
 * @property int $id
 * @property string $path
 * @property array $notes_data
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class DirectoryNote extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'path' => true,
        'notes_data' => true,
        'created' => true,
        'modified' => true,
    ];
}
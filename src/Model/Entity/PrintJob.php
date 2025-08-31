<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PrintJob Entity
 *
 * @property int $id
 * @property int $user_id
 * @property \Cake\I18n\DateTime $timestamp
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\User $user
 */
class PrintJob extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'user_id' => true,
        'timestamp' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Virtual fields
     */
    protected array $_virtual = ['formatted_timestamp'];

    /**
     * Get formatted timestamp for display
     */
    protected function _getFormattedTimestamp()
    {
        if ($this->timestamp) {
            return $this->timestamp->format('M j, Y g:i A');
        }
        return '';
    }
}

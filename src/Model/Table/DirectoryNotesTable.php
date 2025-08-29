<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DirectoryNotes Model
 *
 * @method \App\Model\Entity\DirectoryNote newEmptyEntity()
 * @method \App\Model\Entity\DirectoryNote newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\DirectoryNote> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryNote get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\DirectoryNote findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\DirectoryNote patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\DirectoryNote> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DirectoryNote|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 */
class DirectoryNotesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('directory_notes');
        $this->setDisplayField('path');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('path')
            ->maxLength('path', 1000)
            ->requirePresence('path', 'create')
            ->allowEmptyString('path') // Allow root ('') path for homepage notes
            ->add('path', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('notes_data', 'create')
            ->allowEmptyArray('notes_data'); // Allow empty arrays for notes

        return $validator;
    }

    /**
     * Get directory notes by path
     */
    public function getByPath(string $path): ?\App\Model\Entity\DirectoryNote
    {
        return $this->find()->where(['path' => $path])->first();
    }

    /**
     * Before save callback to ensure notes_data is JSON
     */
    public function beforeSave($event, $entity, $options)
    {
        if (isset($entity->notes_data)) {
            if (is_array($entity->notes_data)) {
                $entity->notes_data = json_encode($entity->notes_data);
            } elseif (is_string($entity->notes_data)) {
                // If it's already a string, validate it's proper JSON
                $decoded = json_decode($entity->notes_data, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // If it's not valid JSON, treat it as content and wrap it in an array
                    $entity->notes_data = json_encode([
                        [
                            'id' => uniqid(),
                            'content' => $entity->notes_data,
                            'created' => date('Y-m-d H:i:s'),
                            'modified' => date('Y-m-d H:i:s')
                        ]
                    ]);
                }
            }
        }
        return true;
    }

    /**
     * After find callback to decode JSON
     */
    public function afterFind($event, $entities, $options)
    {
        // Convert to array to handle both single entities and collections uniformly
        $entityArray = is_array($entities) ? $entities : [$entities];
        
        foreach ($entityArray as $entity) {
            if (isset($entity->notes_data) && is_string($entity->notes_data)) {
                $decoded = json_decode($entity->notes_data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $entity->notes_data = $decoded;
                } else {
                    // If JSON decode fails, set to empty array
                    $entity->notes_data = [];
                }
            }
        }
        
        return $entities;
    }
}
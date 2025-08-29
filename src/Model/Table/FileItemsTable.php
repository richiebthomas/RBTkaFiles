<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FileItems Model
 *
 * @method \App\Model\Entity\FileItem newEmptyEntity()
 * @method \App\Model\Entity\FileItem newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\FileItem> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FileItem get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\FileItem findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\FileItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\FileItem> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FileItem|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\FileItem saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\FileItem> saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FileItem> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FileItem> deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FileItem> deleteManyOrFail(iterable $entities, array $options = [])
 */
class FileItemsTable extends Table
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

        $this->setTable('file_items');
        $this->setDisplayField('name');
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('type')
            ->inList('type', ['file', 'folder'])
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('path')
            ->maxLength('path', 1000)
            ->requirePresence('path', 'create')
            ->notEmptyString('path');

        $validator
            ->scalar('parent_path')
            ->maxLength('parent_path', 1000)
            ->allowEmptyString('parent_path');

        $validator
            ->scalar('mime_type')
            ->maxLength('mime_type', 100)
            ->allowEmptyString('mime_type');

        $validator
            ->integer('size')
            ->allowEmptyString('size');

        $validator
            ->scalar('filename_on_disk')
            ->maxLength('filename_on_disk', 255)
            ->allowEmptyString('filename_on_disk');

        return $validator;
    }

    /**
     * Get items in a specific path
     */
    public function findByPath(string $path = ''): SelectQuery
    {
        return $this->find()
            ->where(['parent_path' => $path])
            ->orderAsc('type')  // folders first
            ->orderAsc('name');
    }

    /**
     * Check if path exists
     */
    public function pathExists(string $path): bool
    {
        return $this->exists(['path' => $path]);
    }

    /**
     * Get item by path
     */
    public function getByPath(string $path): ?\App\Model\Entity\FileItem
    {
        return $this->find()->where(['path' => $path])->first();
    }

    /**
     * Create folder structure recursively if needed
     */
    public function ensurePathExists(string $path): bool
    {
        if (empty($path) || $this->pathExists($path)) {
            return true;
        }

        $pathParts = explode('/', trim($path, '/'));
        $currentPath = '';
        $parentPath = '';

        foreach ($pathParts as $part) {
            $parentPath = $currentPath;
            $currentPath = $currentPath ? $currentPath . '/' . $part : $part;

            if (!$this->pathExists($currentPath)) {
                $folder = $this->newEntity([
                    'name' => $part,
                    'type' => 'folder',
                    'path' => $currentPath,
                    'parent_path' => $parentPath,
                ]);

                if (!$this->save($folder)) {
                    return false;
                }
            }
        }

        return true;
    }
}

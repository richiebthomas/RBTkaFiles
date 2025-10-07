<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pads Model
 *
 * @method \App\Model\Entity\Pad newEmptyEntity()
 * @method \App\Model\Entity\Pad newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Pad> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pad get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Pad findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Pad patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Pad> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pad|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 */
class PadsTable extends Table
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

        $this->setTable('pads');
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

        return $validator;
    }
}


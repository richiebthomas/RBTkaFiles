<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Visits Model
 *
 * @method \App\Model\Entity\Visit newEmptyEntity()
 * @method \App\Model\Entity\Visit newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Visit> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Visit get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Visit findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Visit patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Visit> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Visit|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Visit saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Visit> saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Visit> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Visit> deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Visit> deleteManyOrFail(iterable $entities, array $options = [])
 */
class VisitsTable extends Table
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

        $this->setTable('visits');
        $this->setDisplayField('id');
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
            ->scalar('ip_address')
            ->maxLength('ip_address', 45)
            ->allowEmptyString('ip_address');

        $validator
            ->scalar('user_agent')
            ->allowEmptyString('user_agent');

        $validator
            ->scalar('referer')
            ->allowEmptyString('referer');

        $validator
            ->scalar('session_id')
            ->maxLength('session_id', 255)
            ->allowEmptyString('session_id');

        return $validator;
    }

    /**
     * Check if a visit already exists for the given session ID
     *
     * @param string $sessionId
     * @return bool
     */
    public function hasVisitForSession(string $sessionId): bool
    {
        return $this->exists(['session_id' => $sessionId]);
    }

    /**
     * Get total visit count
     *
     * @return int
     */
    public function getTotalVisits(): int
    {
        return $this->find()->count();
    }

    /**
     * Get visits for a specific date range
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \Cake\ORM\Query
     */
    public function getVisitsInDateRange(\DateTime $startDate, \DateTime $endDate): SelectQuery
    {
        return $this->find()
            ->where([
                'created >=' => $startDate,
                'created <=' => $endDate
            ])
            ->order(['created' => 'DESC']);
    }
}

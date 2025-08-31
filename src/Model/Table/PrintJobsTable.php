<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PrintJobs Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\PrintJob newEmptyEntity()
 * @method \App\Model\Entity\PrintJob newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\PrintJob[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PrintJob get($primaryKey, $options = [])
 * @method \App\Model\Entity\PrintJob findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\PrintJob patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PrintJob[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PrintJob|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PrintJob saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PrintJob[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrintJob[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrintJob[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrintJob[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class PrintJobsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('prints_taken');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
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
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->dateTime('timestamp')
            ->notEmptyDateTime('timestamp');

        $validator
            ->scalar('name_used')
            ->maxLength('name_used', 255)
            ->notEmptyString('name_used');

        $validator
            ->scalar('file_path')
            ->maxLength('file_path', 500)
            ->allowEmptyString('file_path');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    /**
     * Get print jobs for a specific user
     *
     * @param int $userId
     * @return \Cake\ORM\Query
     */
    public function findByUserId(int $userId): Query
    {
        return $this->find()
            ->where(['user_id' => $userId])
            ->order(['timestamp' => 'DESC']);
    }

    /**
     * Get recent print jobs
     *
     * @param int $limit
     * @return \Cake\ORM\Query
     */
    public function findRecent(int $limit = 10): Query
    {
        return $this->find()
            ->contain(['Users'])
            ->order(['timestamp' => 'DESC'])
            ->limit($limit);
    }
}

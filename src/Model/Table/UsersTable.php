<?php
// File: src/Model/Table/UsersTable.php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Database\Schema\TableSchemaInterface;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Override schema to force JSON type mapping
     */
    public function getSchema(): TableSchemaInterface
    {
        $schema = parent::getSchema();

        if ($schema->hasColumn('prints')) {
            $schema = $schema->setColumnType('prints', 'json');
        }

        return $schema;
    }
}

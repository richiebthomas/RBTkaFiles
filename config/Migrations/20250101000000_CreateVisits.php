<?php
use Migrations\AbstractMigration;

class CreateVisits extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('visits');
        $table
            ->addColumn('ip_address', 'string', ['limit' => 45, 'null' => true])
            ->addColumn('user_agent', 'text', ['null' => true])
            ->addColumn('referer', 'text', ['null' => true])
            ->addColumn('session_id', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addColumn('modified', 'datetime', ['null' => false])
            ->addIndex(['session_id'])
            ->addIndex(['created'])
            ->create();
    }
}

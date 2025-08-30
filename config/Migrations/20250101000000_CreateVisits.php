<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateVisits extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('visits');
        $table->addColumn('ip_address', 'string', [
            'default' => null,
            'limit' => 45,
            'null' => true,
        ]);
        $table->addColumn('user_agent', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('referer', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('session_id', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addIndex(['session_id']);
        $table->addIndex(['created']);
        $table->create();
    }
}

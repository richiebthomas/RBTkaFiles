<?php
use Migrations\AbstractMigration;

class CreatePads extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('pads');
        $table
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addColumn('modified', 'datetime', ['null' => false])
            ->addIndex(['created'])
            ->create();
    }
}


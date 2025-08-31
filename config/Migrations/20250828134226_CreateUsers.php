<?php
use Migrations\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table
            ->addColumn('roll_number', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addColumn('modified', 'datetime', ['null' => false])
            ->addColumn('prints', 'text', [
                'null' => false,
                'comment' => 'Serialized/JSON array of prints',
            ])
            ->create();
    }
}

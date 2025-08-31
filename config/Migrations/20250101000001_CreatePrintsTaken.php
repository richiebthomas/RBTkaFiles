<?php
use Migrations\AbstractMigration;

class CreatePrintsTaken extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('prints_taken');
        $table
            ->addColumn('user_id', 'integer', [
                'null' => false,
                'comment' => 'Foreign key to users table'
            ])
            ->addColumn('timestamp', 'datetime', [
                'null' => false,
                'comment' => 'When the print was taken'
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ])
            ->addIndex(['user_id'])
            ->addIndex(['timestamp'])
            ->addIndex(['created'])
            ->create();

        // Add foreign key constraint
        $table->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE'
        ]);
    }
}

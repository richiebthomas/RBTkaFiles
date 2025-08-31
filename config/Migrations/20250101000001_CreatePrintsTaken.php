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
            ->addColumn('name_used', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Name used for the print'
            ])
            ->addColumn('file_path', 'string', [
                'limit' => 500,
                'null' => true,
                'comment' => 'Path of the file that was printed'
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

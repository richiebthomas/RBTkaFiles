<?php
use Migrations\AbstractMigration;

class CreateDirectoryNotes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('directory_notes');
        $table
            ->addColumn('path', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addColumn('modified', 'datetime', ['null' => false])
            ->addColumn('notes_data', 'text', [
                'null' => false,
                'comment' => 'Array of notes with timestamps and content',
            ])
            ->create();
    }
}

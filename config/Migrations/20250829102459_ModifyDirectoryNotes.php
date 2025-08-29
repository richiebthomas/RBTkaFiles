<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class ModifyDirectoryNotes extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('directory_notes');
        $table->addColumn('path', 'string', [
            'default' => null,
            'limit' => 1000,
            'null' => false,
        ]);
        $table->addColumn('notes_data', 'json', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addIndex(['path'], [
            'name' => 'BY_PATH',
            'unique' => true,
        ]);
        $table->create();
    }
}

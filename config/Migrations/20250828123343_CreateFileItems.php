<?php
use Migrations\AbstractMigration;

class CreateFileItems extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('file_items');
        $table
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('type', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('path', 'string', ['limit' => 1000, 'null' => false])
            ->addColumn('parent_path', 'string', ['limit' => 1000, 'null' => true])
            ->addColumn('mime_type', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('size', 'integer', ['null' => true])
            ->addColumn('filename_on_disk', 'string', ['limit' => 255, 'null' => true])
            ->addIndex(['path'], ['name' => 'BY_PATH'])
            ->addIndex(['parent_path'], ['name' => 'BY_PARENT_PATH'])
            ->addIndex(['mime_type'], ['name' => 'BY_MIME_TYPE'])
            ->addIndex(['size'], ['name' => 'BY_SIZE'])
            ->addIndex(['filename_on_disk'], ['name' => 'BY_FILENAME_ON_DISK'])
            ->create();
    }
}

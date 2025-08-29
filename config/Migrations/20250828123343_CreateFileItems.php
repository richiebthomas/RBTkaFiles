<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateFileItems extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('file_items');
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('type', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('path', 'string', [
            'default' => null,
            'limit' => 1000,
            'null' => false,
        ]);
        $table->addColumn('parent_path', 'string', [
            'default' => null,
            'limit' => 1000,
            'null' => true,
        ]);
        $table->addColumn('mime_type', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => true,
        ]);
        $table->addColumn('size', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('filename_on_disk', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addIndex([
            'path',
        
            ], [
            'name' => 'BY_PATH',
            'unique' => false,
        ]);
        $table->addIndex([
            'parent_path',
        
            ], [
            'name' => 'BY_PARENT_PATH',
            'unique' => false,
        ]);
        $table->addIndex([
            'mime_type',
        
            ], [
            'name' => 'BY_MIME_TYPE',
            'unique' => false,
        ]);
        $table->addIndex([
            'size',
        
            ], [
            'name' => 'BY_SIZE',
            'unique' => false,
        ]);
        $table->addIndex([
            'filename_on_disk',
        
            ], [
            'name' => 'BY_FILENAME_ON_DISK',
            'unique' => false,
        ]);
        $table->create();
    }
}

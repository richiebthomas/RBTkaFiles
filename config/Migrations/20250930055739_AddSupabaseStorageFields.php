<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddSupabaseStorageFields extends BaseMigration
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
        
        $table->addColumn('supabase_path', 'string', [
            'default' => null,
            'null' => true,
            'limit' => 1000,
            'comment' => 'Path in Supabase storage'
        ]);
        
        $table->addColumn('storage_type', 'string', [
            'default' => 'local',
            'null' => false,
            'limit' => 20,
            'comment' => 'Storage type: local, supabase'
        ]);
        
        $table->update();
    }
}

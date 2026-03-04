<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddTimestampsToFileItems extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('file_items');

        // Add created/modified columns if they don't already exist
        if (!$table->hasColumn('created')) {
            $table->addColumn('created', 'datetime', [
                'default' => '0000-00-00 00:00:00',
                'null' => false,
                'comment' => 'Upload/creation datetime for files and folders',
            ]);
        }

        if (!$table->hasColumn('modified')) {
            $table->addColumn('modified', 'datetime', [
                'default' => '0000-00-00 00:00:00',
                'null' => false,
                'comment' => 'Last modification datetime for files and folders',
            ]);
        }

        $table->update();

        // Backfill any existing NULL timestamps with zero datetime
        // (per your requirement to "fill it with zeros")
        $this->execute("UPDATE file_items SET created = '0000-00-00 00:00:00' WHERE created IS NULL");
        $this->execute("UPDATE file_items SET modified = '0000-00-00 00:00:00' WHERE modified IS NULL");
    }
}


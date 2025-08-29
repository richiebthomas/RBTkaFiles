<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddPrintsToUsers extends BaseMigration
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
        $table = $this->table('users');
        $table->addColumn('prints', 'json', [
            'default' => null,
            'null' => false,
        ]);
        $table->addIndex([
            'prints',
        
            ], [
            'name' => 'BY_PRINTS',
            'unique' => false,
        ]);
        $table->update();
    }
}

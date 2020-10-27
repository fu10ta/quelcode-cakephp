<?php

use Migrations\AbstractMigration;

class AddEightColumnsToBidinfo extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('bidinfo');
        $table->addColumn('buyer_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('buyer_address', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('buyer_phone_number', 'string', [
            'default' => null,
            'limit' => 13,
            'null' => true,
        ]);
        $table->addColumn('is_sent', 'boolean', [
            'default' => 0,
            'null' => false,
        ]);
        $table->addColumn('is_received', 'boolean', [
            'default' => 0,
            'null' => false,
        ]);
        $table->addColumn('is_seller_rated', 'boolean', [
            'default' => 0,
            'null' => false,
        ]);
        $table->addColumn('is_buyer_rated', 'boolean', [
            'default' => 0,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'update' => 'CURRENT_TIMESTAMP',
            'null' => false,
        ]);
        $table->update();
    }
}

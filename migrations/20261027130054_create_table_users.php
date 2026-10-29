<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTableUsers extends AbstractMigration {
    public function change() : void {
        $table = $this->table('users', [
            'id' => false,
            'primary_key' => ['id'],
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('id', 'uuid')
            ->addColumn('first_name', 'string')
            ->addColumn('last_name', 'string')
            ->addColumn('active', 'tinyinteger', ['limit' => 1, 'default' => 1])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true, ])
            ->addIndex(['active'])
            ->create();
    }
}

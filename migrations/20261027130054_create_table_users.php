<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTableUsers extends AbstractMigration {
    public function change():void {
        $table = $this->table('users', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table
            ->addColumn('id', 'uuid')
            ->addColumn('first_name', 'string', [
                'limit' => 100,
            ])
            ->addColumn('last_name', 'string', [
                'limit' => 100,
            ])
            ->addColumn('active', 'boolean', [
                'default' => true,
            ])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', [
                'null' => true,
            ])
            ->addIndex(['active'])
            ->create();
    }
}

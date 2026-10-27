<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTimeEntriesTable extends AbstractMigration {
    public function change():void {
        $table = $this->table('time_entries');

        $table
            ->addColumn('user_id', 'uuid')
            ->addColumn('date', 'date')
            ->addColumn('duration', 'time')
            ->addColumn('activity_role', 'string', [
                'limit' => 100,
            ])
            ->addColumn('group_id', 'uuid', [
                'null' => true,
            ])
            ->addColumn('comment', 'text', [
                'null' => true,
            ])
            ->addColumn('approved_by', 'uuid', [
                'null' => true,
            ])
            ->addColumn('approved_at', 'datetime', [
                'null' => true,
            ])
            ->addColumn('exported_by', 'uuid', [
                'null' => true,
            ])
            ->addColumn('exported_at', 'datetime', [
                'null' => true,
            ])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', [
                'null' => true,
            ])
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'delete' => 'RESTRICT',
                    'update' => 'CASCADE',
                ]
            )
            ->addIndex(['user_id'])
            ->addIndex(['date'])
            ->addIndex(['group_id'])
            ->addIndex(['approved_at'])
            ->addIndex(['exported_at'])
            ->create();
    }
}

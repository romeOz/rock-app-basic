<?php

namespace apps\common\migrations\mysql;


use rock\db\Migration;

class AccessRolesItemsMigration extends Migration
{
    public $table = 'access_roles_items';

    public function up()
    {
            $this->createTable(
                $this->table,
                [
                    'role' => 'VARCHAR(64) NOT NULL',
                    'item' => 'VARCHAR(64) NOT NULL',
                ],
                'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB',
                true
            );

        $this->batchInsert(
            $this->table,
            ['role', 'item'],
            [
                ['godmode', 'admin'],
                ['admin', 'editor'],
                ['admin', 'delete_post'],
                ['editor', 'user'],
                ['editor', 'create_post'],
                ['editor', 'update_post'],
                ['moderator','user'],
                ['user','guest'],
                ['guest','read_post'],
            ]
        );

        // indexes

        $this->addPrimaryKey("idx_{$this->table}__primary",$this->table,['role', 'item']);
        $this->addForeignKey("fk_{$this->table}__role", $this->table,'role', 'access_items', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey("fk_{$this->table}__item", $this->table,'item', 'access_items', 'name', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable($this->table, true);
    }
} 
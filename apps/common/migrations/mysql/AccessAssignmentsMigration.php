<?php

namespace apps\common\migrations\mysql;


use apps\common\migrations\CommonMigration;

class AccessAssignmentsMigration extends CommonMigration
{
    public $table = 'access_assignments';

    public function up()
    {
        $this->createTable(
            $this->table,
            [
                'user_id' => 'INTEGER unsigned NOT NULL',
                'item' => 'VARCHAR(64) NOT NULL',
            ],
            'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB',
            true
        );

        // insert data

        $this->batchInsert(
            $this->table,
            ['user_id', 'item'],
            [
                [2, 'godmode'],
                [1, 'editor'],
                [3, 'user'],
            ]
        );

        // indexes

        $this->addPrimaryKey("idx_{$this->table}__primary",$this->table,['user_id', 'item']);
        $this->addForeignKey("fk_{$this->table}__user_id", $this->table,'user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey("fk_{$this->table}__item", $this->table,'item', 'access_items', 'name', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable($this->table, true);
    }
}
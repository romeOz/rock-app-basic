<?php

namespace apps\common\migrations\mysql;

use rock\db\Migration;
use rock\rbac\Permission;
use rock\rbac\RBACInterface;
use rock\rbac\Role;
use rock\rbac\UserRole;

class AccessItemsMigration extends Migration
{
    public $table = 'access_items';

    public function up()
    {
        $this->createTable(
            $this->table,
            [
                'name' => 'VARCHAR(64) NOT NULL PRIMARY KEY',
                'type' => 'TINYINT(2) NOT NULL DEFAULT 1',
                'description' => "VARCHAR(255) NOT NULL DEFAULT ''",
                'data' => "TEXT NOT NULL",
                'order_index' => 'INT unsigned NOT NULL DEFAULT 0',
            ],
            'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB',
            true
        );

        // insert data

        $this->batchInsert(
            $this->table,
            ['name', 'type', 'description', 'data', 'order_index'],
            [
                ['godmode', RBACInterface::TYPE_ROLE, 'super admin', serialize(new Role), 999],
                ['admin', RBACInterface::TYPE_ROLE, 'administrator',serialize(new Role), 998],
                ['editor', RBACInterface::TYPE_ROLE, 'editor', serialize(new Role), 997],
                ['moderator', RBACInterface::TYPE_ROLE, 'moderator', serialize(new Role), 996],
                ['user', RBACInterface::TYPE_ROLE, 'user', serialize(new UserRole), 995],
                ['guest', RBACInterface::TYPE_ROLE, 'guest', serialize(new Role), 994],
                ['read_post', RBACInterface::TYPE_PERMISSION, 'read post', serialize(new Permission), 0],
                ['create_post', RBACInterface::TYPE_PERMISSION, 'create post', serialize(new Permission), 0],
                ['update_post', RBACInterface::TYPE_PERMISSION, 'update post', serialize(new Permission), 0],
                ['delete_post', RBACInterface::TYPE_PERMISSION, 'delete post', serialize(new Permission), 0],
            ]
        );

        // indexes

        $this->createIndex("idx_{$this->table}__type", $this->table, 'type');
    }

    public function down()
    {
        $this->dropTable($this->table, true);
    }
} 
<?php

namespace apps\common\migrations\mysql;

use apps\common\migrations\CommonMigration;
use rock\helpers\Inflector;
use rock\helpers\NumericHelper;
use rock\Rock;

class UsersMigration extends CommonMigration
{
    public $table = 'users';

    public function up()
    {
        $this->createTable(
            $this->table,
            [
                'id' => 'INTEGER unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
                'username' => 'VARCHAR(100) NOT NULL',
                'username_hash' => 'BINARY(16) NOT NULL',
                'email' => 'VARCHAR(100) NOT NULL',
                'email_hash' => 'BINARY(16) NOT NULL',
                'password' => "CHAR(60) NOT NULL DEFAULT ''",
                'token' => "VARCHAR(50)" ,
                'status' => 'TINYINT(2) unsigned NOT NULL DEFAULT 2',
                'ctime' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()',
                'login_last' => 'TIMESTAMP',
                'url' => "VARCHAR(512) NOT NULL DEFAULT ''",
            ],
            'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB',
            true
        );

        $this->createIndex("idx_{$this->table}__username_hash", $this->table, 'username_hash', true);
        $this->createIndex("idx_{$this->table}__email_hash", $this->table, 'email_hash', true);
        $this->createIndex("idx_{$this->table}__status", $this->table, 'status');

        $security = Rock::$app->security;
        $this->batchInsert(
            $this->table,
            ['username', 'username_hash', 'password', 'token', 'email', 'email_hash', 'status', 'url'],
            [
                ['Tom', $this->hash('Tom'), $security->generatePasswordHash('demo'), $security->generateRandomString() . '_' . time(), 'tom@gmail.com', $this->hash('tom@gmail.com'), 2, ''],
                ['Jane', $this->hash('Jane'), $security->generatePasswordHash('demo'), $security->generateRandomString() . '_' . time(), 'jane@hotmail.com', $this->hash('jane@hotmail.com'), 2, ''],
                ['Linda',$this->hash('Linda'), $security->generatePasswordHash('demo'), $security->generateRandomString() . '_' . time(), 'linda@gmail.com', $this->hash('linda@gmail.com'), 3, '/linda/'],
            ]
        );
    }

    public function down()
    {
        $this->dropTable($this->table, true);
    }

    protected function hash($value)
    {
        return NumericHelper::hexToBin(md5($value));
    }

    protected function prepareUrl($username)
    {
        return mb_strtolower('/profile/' . $this->translitUsername($username) . '/', 'UTF-8');
    }

    /**
     * Translit username.
     *
     * @param string $username
     * @return string
     */
    protected function translitUsername($username)
    {
        return Inflector::slug(preg_replace('/[\_\-]+/iu','-', $username));
    }
}
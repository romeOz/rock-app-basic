<?php

namespace apps\common\migrations;


use rock\db\Migration;

abstract class CommonMigration extends Migration
{
    public $table;
    public $pathFixture;
    public $plain = false;

    public function execFixture()
    {
        if (!isset($this->pathFixture)) {
            return;
        }

        if (!file_exists($this->pathFixture)) {
            throw new \Exception("Unknown file: {$this->pathFixture}.");
        }

        if ($this->plain) {
            $this->connection->pdo->exec(file_get_contents($this->pathFixture));
            return;
        }
        $lines = explode(';', file_get_contents($this->pathFixture));
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $this->connection->pdo->exec($line);
            }
        }

    }
}
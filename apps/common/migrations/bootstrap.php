<?php

use apps\common\migrations\AccessItemsMigration;
use apps\common\migrations\AccessRolesItemsMigration;
use apps\common\migrations\AccessAssignmentsMigration;
use apps\common\migrations\UsersMigration;
use rock\Rock;

require(dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php');

Rock::$app = new Rock();
Rock::$app->language = 'en';

if (!$config = require(dirname(dirname(__DIR__)) . '/common/configs/configs.php')) {
    die('configs is empty/not found');
}

if ($env = array_slice($argv, 1)) {

    $db = [
        'class' => \rock\db\Connection::className(),
        'username' => $env[0],
        'password' => $env[1],
        'charset' => 'utf8',
        'dsn' => 'mysql:host=localhost;dbname=rockdemo;charset=utf8',
        'tablePrefix' => 'spt_',
        'aliasSeparator' => '__',
        'enableSchemaCache' => true,
        //'enableQueryCache'  => true
    ];
    $path = dirname(dirname(__DIR__)) . '/common/configs/classes.php';
    $classes = require($path);
    $config['components']['db'] = $classes['db'] = $db;
    file_put_contents($path, "<?php\nreturn " .var_export($classes, true) . ';');
}


Rock::$components = $config['components'];
unset($config['components']);
Rock::$config = $config;

\rock\di\Container::addMulti(Rock::$components);

(new UsersMigration())->up();
(new AccessItemsMigration())->up();
(new AccessRolesItemsMigration())->up();
(new AccessAssignmentsMigration())->up();
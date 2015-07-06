<?php

use apps\common\migrations\mysql\AccessAssignmentsMigration;
use apps\common\migrations\mysql\AccessItemsMigration;
use apps\common\migrations\mysql\AccessRolesItemsMigration;
use apps\common\migrations\mysql\UsersMigration;
use rock\Rock;

require(dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php');

Rock::$app = new Rock();
Rock::$app->language = 'en';

if (!$config = require(dirname(dirname(__DIR__)) . '/common/configs/configs.php')) {
    die('configs is empty/not found');
}

Rock::$components = $config['components'];
unset($config['components']);
Rock::$config = $config;

\rock\di\Container::registerMulti(Rock::$components);

(new UsersMigration())->up();
(new AccessItemsMigration())->up();
(new AccessRolesItemsMigration())->up();
(new AccessAssignmentsMigration())->up();
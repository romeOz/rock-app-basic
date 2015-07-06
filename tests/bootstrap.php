<?php
use rock\base\Alias;
use rock\Rock;

$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require($composerAutoload);
}

$loader->addPsr4('rockunit\\', __DIR__);

$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = 'site.com';
$_SERVER['REQUEST_URI'] = '/';
$_SESSION = [];
date_default_timezone_set('UTC');


Rock::$app = new Rock();
Rock::$app->language = 'en';

define('ROCKUNIT_RUNTIME', __DIR__ . '/runtime');
Alias::setAlias('rockunit', __DIR__);
\rock\base\Alias::setAlias('web', '/assets');

if (!$config = require(dirname(__DIR__) . '/apps/common/configs/configs.php')) {
    die('configs is empty/not found');
}

$components = require(__DIR__ . '/data/config.php');

if (!empty($components['classes'])) {
    $config['components'] = \rock\helpers\ArrayHelper::merge(
        $config['components'] ? : [],
        $components['classes']
    );
}

Rock::$components = $config['components'];
unset($config['components']);
Rock::$config = $config;
\rock\di\Container::registerMulti(Rock::$components);

\rock\exception\ErrorHandler::$logged = false;


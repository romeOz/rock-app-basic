<?php
error_reporting(-1);
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('need to use PHP version 5.4.x or greater');
}

require(dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php');

defined('ROCK_DEBUG') or define('ROCK_DEBUG', true);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

\rock\Rock::$app = new \rock\Rock();

// catch error
\rock\exception\ErrorHandler::register();

$config = require(dirname(__DIR__) .'/configs/configs.php');

// bootstrap application
\rock\Rock::bootstrap($config);


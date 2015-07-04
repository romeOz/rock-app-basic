<?php
// Config scope "frontend"

$config = require(dirname(dirname(__DIR__)) . '/common/configs/configs.php');

\rock\base\Alias::setAlias('scope', '@frontend');
\rock\base\Alias::setAlias('views', '@frontend/views');
\rock\base\Alias::setAlias('runtime', '@frontend/runtime');
\rock\base\Alias::setAlias('ns', '@frontend.ns');

$request = new \rock\request\Request();
\rock\base\Alias::setAlias('link.home', $request->getHostInfo());
\rock\base\Alias::setAlias('link.ajax', '@link.home/ajax');
\rock\base\Alias::setAlias('email', 'support@' . $request->getHost());

$config['components'] = \rock\helpers\ArrayHelper::merge(
    $config['components'],
    require(__DIR__ . '/classes.php'),
    require(__DIR__ . '/models.php'),
    require(__DIR__ . '/controllers.php')
);
return $config;


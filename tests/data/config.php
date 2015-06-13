<?php

use apps\common\migrations\mysql\UsersMigration;
use rock\log\Log;
use rockunit\mocks\SessionMock;

return [
    'databases' => [
        'mysql' => [
            'dsn' => 'mysql:host=127.0.0.1;dbname=rocktest',
            'username' => 'travis',
            'password' => '',
            'fixture' => __DIR__ . '/mysql.sql',
            'migrations' => [
                ['class' => UsersMigration::className()]
            ]
        ],
    ],
    'classes' => [
        'log' => [
            'class' => Log::className(),
            'path' => __DIR__ . '/runtime/logs'
        ],
//        'cache' => [
//            'class' => \rock\cache\CacheStub::className()
//        ],
        'session' => [
            'class' => SessionMock::className(),
        ],
//        'cookie' => [
//            'class' => CookieMock::className(),
//        ],
//        'execute' => [
//            'class' => CacheExecute::className(),
//            'path' => '@tests/runtime/cache/_execute'
//        ],
//        'rbac' => [
//            'class' => PhpManager::className(),
//            'path' => '@rockunit/data/rbac/rbac.php'
//        ]
    ]
];

<?php
return [
    'db' =>[
        'class' => 'rock\\db\\Connection',
        'username' => 'rock',
        'password' => 'rock',
        'charset' => 'utf8',
        'dsn' => 'mysql:host=localhost;dbname=rockdemo;charset=utf8',
        'tablePrefix' => 'spt_',
        'aliasSeparator' => '__',
        'enableSchemaCache' => true,
    ],

    'i18n' => [
        'class' => \rock\i18n\i18n::className(),
        'pathsDicts' => [
            'ru' => [
                '@common/messages/ru/lang.php',
                '@common/messages/ru/validate.php',
                '@frontend/messages/ru/lang.php',
            ],
            'en' => [
                '@common/messages/en/lang.php',
                '@common/messages/en/validate.php',
                '@frontend/messages/en/lang.php',
            ]
        ]
    ],
    'execute' => [
        'class' => \rock\execute\CacheExecute::className(),
        'path' => '@common/runtime/execute'
    ],

    'markdown' =>[
        'class' => \rock\markdown\Markdown::className(),
        'handlerLinkByUsername' => [\apps\common\configs\MarkdownProperties::className(), 'handlerLinkByUsername']
    ],
    'mail' => [
        'From' => \rock\base\Alias::getAlias('@email'),
    ],
];